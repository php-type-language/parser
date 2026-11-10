<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\PartialResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Source\StringSource;
use TypeLang\Parser\Internal\CompiledExecutor;

/**
 * Tells whether a source is a whole sentence of the grammar, builds nothing.
 *
 * @template-extends CompiledExecutor<null>
 */
final class SyntaxChecker extends CompiledExecutor
{
    /**
     * @param int<0, max> $entry the rule the reading starts with: a kept rule of the grammar
     */
    public function isValid(string $source, int $entry = self::Type): bool
    {
        $result = $this->parser
            ->withInitial($entry)
            ->analyze(new StringSource($source), Mode::SyntaxCheck);

        return $result instanceof SuccessfulResult && !$result instanceof PartialResult;
    }

    /**
     * Identifiers of the tokens a source is made of, the end of the input and
     * the skipped tokens are not in it.
     *
     * @return list<int>
     */
    public function tokenize(string $source): array
    {
        $result = [];

        foreach ($this->lexer->lex(new StringSource($source)) as $token) {
            if ($token->channel === Channel::Hidden || $token->channel === Channel::EndOfInput) {
                continue;
            }

            $result[] = $token->id;
        }

        return $result;
    }
}
