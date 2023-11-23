<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\Context;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserCombinator;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\File;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * @psalm-type GrammarConfigArray = array{
 *  initial: int<0, max>|non-empty-string,
 *  tokens: array{
 *      default: array<non-empty-string, non-empty-string>
 *  },
 *  skip: array<non-empty-string>,
 *  transitions: array,
 *  grammar: array<int<0, max>|non-empty-string, RuleInterface>,
 *  reducers: array<int<0, max>|non-empty-string, callable(Context, mixed):mixed>
 * }
 */
final class Parser implements ParserInterface
{
    private readonly ParserCombinator $parser;

    public readonly Lexer $lexer;

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

    private readonly BuilderInterface $builder;

    /**
     * @var int<0, max>
     */
    public int $lastProcessedTokenOffset = 0;

    public function __construct(
        public readonly bool $tolerant = false,
        public readonly bool $conditional = true,
        public readonly bool $shapes = true,
        public readonly bool $callables = true,
        public readonly bool $literals = true,
    ) {
        /** @psalm-var GrammarConfigArray $grammar */
        $grammar = require __DIR__ . '/../resources/grammar.php';

        /** @var \WeakMap<TokenInterface, StringLiteralNode> */
        $this->stringPool = new \WeakMap();

        /** @var \WeakMap<TokenInterface, IntLiteralNode> */
        $this->integerPool = new \WeakMap();

        $this->builder = new Builder($grammar['reducers']);
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
                ParserConfigsInterface::CONFIG_AST_BUILDER => $this->builder,
                ParserConfigsInterface::CONFIG_ALLOW_TRAILING_TOKENS => $this->tolerant,
            ],
        );
    }

    /**
     * @psalm-param GrammarConfigArray $grammar
     */
    private function createLexer(array $grammar): Lexer
    {
        $lexer = new Lexer(
            tokens: $grammar['tokens']['default'],
            skip: $grammar['skip'],
        );

        $lexer->disableUnrecognizedTokenException();

        return $lexer;
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): ?TypeStatement
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::new($source);

        $allowedNestingLevel = (int)\ini_get('xdebug.max_nesting_level');

        try {
            \ini_set('xdebug.max_nesting_level', -1);

            try {
                foreach ($this->parser->parse($source) as $stmt) {
                    if ($stmt instanceof TypeStatement) {
                        $context = $this->parser->getLastExecutionContext();
                        $token = $context->buffer->current();

                        $this->lastProcessedTokenOffset = $token->getOffset();

                        return $stmt;
                    }
                }

                return null;
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
