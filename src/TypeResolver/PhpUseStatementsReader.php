<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Parser\TypeResolver\PhpUseStatementsReader\NamespaceFinder;
use TypeLang\Parser\TypeResolver\PhpUseStatementsReader\ReflectionSourcePrefixReader;

final readonly class PhpUseStatementsReader
{
    private ReflectionSourcePrefixReader $reader;
    private NamespaceFinder $namespace;

    public function __construct()
    {
        $this->reader = new ReflectionSourcePrefixReader();
        $this->namespace = new NamespaceFinder();
    }

    /**
     * Return array of use statements from class.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return array<int|non-empty-string, non-empty-string>
     */
    public function getClassUseStatements(\ReflectionClass $class): array
    {
        try {
            $header = $this->reader->readClassHeader($class);
        } catch (\Throwable) {
            $header = '';
        }

        return [...$this->parse($class->getNamespaceName(), $header)];
    }

    /**
     * Return array of use statements from function.
     *
     * @param \ReflectionFunctionAbstract $function
     *
     * @return array<int|non-empty-string, non-empty-string>
     */
    public function getFunctionUseStatements(\ReflectionFunctionAbstract $function): array
    {
        try {
            $header = $this->reader->readFunctionHeader($function);
        } catch (\Throwable) {
            $header = '';
        }

        return [...$this->parse($function->getNamespaceName(), $header)];
    }

    /**
     * @return \Iterator<array-key, \PhpToken>
     */
    private function lex(string $source): \Iterator
    {
        yield from \PhpToken::tokenize($source);
    }

    /**
     * Parse the use statements from read source by tokenizing and reading the
     * tokens. Returns an array of use statements and aliases.
     *
     * @return \Iterator<int|non-empty-string, non-empty-string>
     */
    private function parse(string $namespace, string $source): \Iterator
    {
        $tokens = $this->lex($source);

        // Rewind tokens iterator to expected namespace start offset
        $tokens = $this->namespace->rewind($namespace, $tokens);

        return $this->readImports($tokens);
    }

    /**
     * Reads the type imports of the current namespace.
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return \Iterator<int|non-empty-string, non-empty-string>
     */
    private function readImports(\Iterator $tokens): \Iterator
    {
        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_WHITESPACE
                || $current->id === \T_COMMENT
                || $current->id === \T_DOC_COMMENT
            ) {
                $tokens->next();

                continue;
            }

            // The first token that does not open a "use" statement ends the
            // import section of the namespace.
            if ($current->id !== \T_USE) {
                break;
            }

            // Skip the "use" keyword.
            $tokens->next();

            foreach ($this->fetchUseStatement($tokens) as [$namespace, $alias]) {
                if ($alias === null) {
                    yield $namespace;
                } else {
                    yield $alias => $namespace;
                }
            }
        }
    }

    /**
     * Reads a single "use" statement and returns the imports it declares.
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function fetchUseStatement(\Iterator $tokens): array
    {
        $buffer = $this->readStatementTokens($tokens);

        $offset = 0;

        // "use function Some\fn;" and "use const Some\CONST;" are imports too
        if (isset($buffer[0]) && ($buffer[0]->id === \T_FUNCTION || $buffer[0]->id === \T_CONST)) {
            $offset = 1;
        }

        $entries = \array_slice($buffer, $offset);
        $prefix = null;

        // A group "use Some\Prefix\{ A, B as C };" shares a common prefix
        $open = $this->offsetOf($entries, '{');

        if ($open !== null) {
            $prefix = $this->readName(\array_slice($entries, 0, $open));

            if ($prefix === '') {
                return [];
            }

            $close = $this->offsetOf($entries, '}') ?? \count($entries);
            $entries = \array_slice($entries, $open + 1, $close - $open - 1);
        }

        return $this->parseEntries($entries, $prefix);
    }

    /**
     * Consumes the tokens of the current statement up to and including the
     * terminating ";"
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return list<\PhpToken>
     */
    private function readStatementTokens(\Iterator $tokens): array
    {
        $buffer = [];

        while ($tokens->valid()) {
            $current = $tokens->current();
            $tokens->next();

            if ($current->text === ';') {
                break;
            }

            if ($current->id === \T_WHITESPACE
                || $current->id === \T_COMMENT
                || $current->id === \T_DOC_COMMENT
            ) {
                continue;
            }

            $buffer[] = $current;
        }

        return $buffer;
    }

    /**
     * Splits a comma-separated list of import entries
     *
     * @param list<\PhpToken> $tokens
     * @param non-empty-string|null $prefix
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function parseEntries(array $tokens, ?string $prefix): array
    {
        $imports = [];
        $entry = [];

        foreach ($tokens as $token) {
            if ($token->text === ',') {
                $import = $this->parseEntry($entry, $prefix);

                if ($import !== null) {
                    $imports[] = $import;
                }

                $entry = [];

                continue;
            }

            $entry[] = $token;
        }

        $import = $this->parseEntry($entry, $prefix);

        if ($import !== null) {
            $imports[] = $import;
        }

        return $imports;
    }

    /**
     * @param list<\PhpToken> $tokens
     *
     * @return int<0, max>|null
     */
    private function offsetOf(array $tokens, string $text): ?int
    {
        foreach ($tokens as $offset => $token) {
            if ($token->text === $text) {
                return $offset;
            }
        }

        return null;
    }

    /**
     * Parses a single "Name (as Alias)?" import entry
     *
     * @param list<\PhpToken> $tokens
     * @param non-empty-string|null $prefix
     *
     * @return array{non-empty-string, non-empty-string|null}|null
     */
    private function parseEntry(array $tokens, ?string $prefix): ?array
    {
        $nameTokens = [];
        $alias = null;
        $afterAs = false;

        foreach ($tokens as $token) {
            if ($token->id === \T_AS) {
                $afterAs = true;

                continue;
            }

            if ($afterAs) {
                if ($token->id === \T_STRING) {
                    $alias = $token->text;
                }

                continue;
            }

            $nameTokens[] = $token;
        }

        $name = $this->readName($nameTokens);

        if ($name === '') {
            return null;
        }

        if ($prefix !== null) {
            $name = $prefix . '\\' . $name;
        }

        return [$name, $alias === '' ? null : $alias];
    }

    /**
     * @param list<\PhpToken> $tokens
     */
    private function readName(array $tokens): string
    {
        $result = '';

        foreach ($tokens as $token) {
            if ($token->id === \T_STRING
                || $token->id === \T_NAME_QUALIFIED
                || $token->id === \T_NAME_FULLY_QUALIFIED
                || $token->id === \T_NS_SEPARATOR
            ) {
                $result .= $token->text;
            }
        }

        return \trim($result, '\\');
    }
}
