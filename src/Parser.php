<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\ContextInterface;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserCombinator;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\File;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Definition\DefinitionStatement;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @psalm-type GrammarConfigArray = array{
 *  initial: int<0, max>|non-empty-string,
 *  tokens: array{
 *      default: array<non-empty-string, non-empty-string>
 *  },
 *  skip: array<non-empty-string>,
 *  transitions: array,
 *  grammar: array<int<0, max>|non-empty-string, RuleInterface>,
 *  reducers: array<int<0, max>|non-empty-string, callable(ContextInterface, mixed):mixed>
 * }
 */
final class Parser implements ParserInterface
{
    private readonly ParserCombinator $parser;

    private readonly Lexer $lexer;

    /**
     * In-memory string literal pool.
     *
     * @var \WeakMap<TokenInterface, StringLiteralNode>
     */
    private readonly \WeakMap $stringPool;

    /**
     * In-memory integer literal pool.
     *
     * @var \WeakMap<TokenInterface, IntLiteralNode>
     */
    private readonly \WeakMap $integerPool;

    public function __construct()
    {
        /** @psalm-var GrammarConfigArray $grammar */
        $grammar = require __DIR__ . '/../resources/grammar.php';

        /** @var \WeakMap<TokenInterface, StringLiteralNode> */
        $this->stringPool = new \WeakMap();

        /** @var \WeakMap<TokenInterface, IntLiteralNode> */
        $this->integerPool = new \WeakMap();

        $this->lexer = $this->createLexer($grammar);
        $this->parser = $this->createParser($this->lexer, $grammar);
    }

    /**
     * @psalm-param GrammarConfigArray $grammar
     */
    private function createParser(LexerInterface $lexer, array $grammar): ParserCombinator
    {
        return new ParserCombinator(
            lexer: $lexer,
            grammar: $grammar['grammar'],
            options: [
                ParserConfigsInterface::CONFIG_INITIAL_RULE => $grammar['initial'],
                ParserConfigsInterface::CONFIG_AST_BUILDER => new Builder($grammar['reducers']),
            ]
        );
    }

    /**
     * @psalm-param GrammarConfigArray $grammar
     */
    private function createLexer(array $grammar): Lexer
    {
        return new Lexer($grammar['tokens']['default'], $grammar['skip']);
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): iterable
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::new($source);

        return $this->executeAndHandleErrors($source, 'Document');
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parseType(#[Language('PHP')] mixed $source): ?TypeStatement
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::new($source);

        foreach ($this->executeAndHandleErrors($source, 'Type') as $type) {
            if ($type instanceof TypeStatement) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param non-empty-literal-string $initial
     *
     * @return iterable<array-key, TypeStatement|DefinitionStatement>
     *
     * @throws ParseException
     */
    private function executeAndHandleErrors(ReadableInterface $source, string $initial): iterable
    {
        $allowedNestingLevel = (int)\ini_get('xdebug.max_nesting_level');

        try {
            \ini_set('xdebug.max_nesting_level', -1);

            try {
                /** @var iterable<array-key, TypeStatement|DefinitionStatement> */
                return $this->parser
                    ->startsAt($initial)
                    ->parse($source)
                ;
            } catch (UnexpectedTokenException $e) {
                throw $this->unexpectedTokenError($e, $source);
            } catch (UnrecognizedTokenException $e) {
                throw $this->unrecognizedTokenError($e, $source);
            } catch (RuntimeExceptionInterface $e) {
                throw $this->runtimeError($e, $source);
            } catch (SemanticException $e) {
                throw $this->semanticError($e, $source);
            } catch (\Throwable $e) {
                throw $this->internalError($e, $source);
            }
        } finally {
            \ini_set('xdebug.max_nesting_level', $allowedNestingLevel);
        }
    }

    private function unexpectedTokenError(UnexpectedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnexpectedToken(
            $token->getValue(),
            $source->getContents(),
            $token->getOffset(),
        );
    }

    private function unrecognizedTokenError(UnrecognizedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedToken(
            $token->getValue(),
            $source->getContents(),
            $token->getOffset(),
        );
    }

    private function semanticError(SemanticException $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromSemanticError(
            $e->getMessage(),
            $source->getContents(),
            $e->getOffset(),
            $e->getCode(),
        );
    }

    private function runtimeError(RuntimeExceptionInterface $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedSyntaxError(
            $source->getContents(),
            $token->getOffset(),
        );
    }

    private function internalError(\Throwable $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromInternalError($source->getContents(), $e);
    }
}
