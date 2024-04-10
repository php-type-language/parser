<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceExceptionInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Lexer\Config\PassthroughHandler;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\Context;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserCombinator;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * @psalm-type GrammarConfigArray = array{
 *  initial: int<0, max>|non-empty-string,
 *  tokens: array{
 *      default: array<array-key, non-empty-string>
 *  },
 *  skip: list<non-empty-string>,
 *  transitions: array,
 *  grammar: array<int<0, max>|non-empty-string, RuleInterface>,
 *  reducers: array<int<0, max>|non-empty-string, callable(Context, mixed):mixed>
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

    private readonly BuilderInterface $builder;

    /**
     * @var int<0, max>
     * @psalm-readonly-allow-private-mutation
     */
    public int $lastProcessedTokenOffset = 0;

    /**
     * @param bool $tolerant Enables or disables tolerant type recognition. If
     *       the option is {@see true}, the parser allows arbitrary text after
     *       the type definition. This mode allows you to recognize types
     *       specified in DocBlocks.
     * @param bool $conditional Enables or disables support for
     *        dependent/conditional types such as `T ? X : Y`.
     * @param bool $shapes Enables or disables support for type shapes
     *        such as `T{key: X}`.
     * @param bool $callables Enables or disables support for callable types
     *        such as `(X, Y): T`.
     * @param bool $literals Enables or disables support for literal types such
     *        as `42` or `"string"`.
     * @param bool $generics Enables or disables support for template arguments
     *        such as `T<X, Y>`.
     * @param bool $union Enables or disables support for logical union types
     *        such as `T | X`.
     * @param bool $intersection Enables or disables support for logical
     *        intersection types such as `T & X`.
     * @param bool $list Enables or disables support for square bracket list
     *        types such as `T[]`.
     */
    public function __construct(
        public readonly bool $tolerant = false,
        public readonly bool $conditional = true,
        public readonly bool $shapes = true,
        public readonly bool $callables = true,
        public readonly bool $literals = true,
        public readonly bool $generics = true,
        public readonly bool $union = true,
        public readonly bool $intersection = true,
        public readonly bool $list = true,
        private readonly SourceFactoryInterface $sources = new SourceFactory(),
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
        return new Lexer(
            tokens: $grammar['tokens']['default'],
            skip: $grammar['skip'],
            onUnknownToken: new PassthroughHandler(),
        );
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): TypeStatement
    {
        $this->lastProcessedTokenOffset = 0;

        try {
            $instance = $this->sources->create($source);

            try {
                foreach ($this->parser->parse($instance) as $stmt) {
                    if ($stmt instanceof TypeStatement) {
                        $context = $this->parser->getLastExecutionContext();

                        if ($context !== null) {
                            $token = $context->buffer->current();

                            $this->lastProcessedTokenOffset = $token->getOffset();
                        }

                        return $stmt;
                    }
                }

                throw new ParseException(
                    message: 'Could not read type statement',
                    code: ParseException::ERROR_CODE_INTERNAL_ERROR,
                );
            } catch (UnexpectedTokenException $e) {
                throw $this->unexpectedTokenError($e, $instance);
            } catch (UnrecognizedTokenException $e) {
                throw $this->unrecognizedTokenError($e, $instance);
            } catch (ParserRuntimeExceptionInterface $e) {
                throw $this->parserRuntimeError($e, $instance);
            } catch (SemanticException $e) {
                throw $this->semanticError($e, $instance);
            } catch (\Throwable $e) {
                throw $this->internalError($e, $instance);
            }
        } catch (SourceExceptionInterface $e) {
            throw new ParseException(
                message: $e->getMessage(),
                code: ParseException::ERROR_CODE_INTERNAL_ERROR,
                previous: $e,
            );
        }
    }

    /**
     * @throws SourceExceptionInterface In case of source content reading error.
     */
    private function unexpectedTokenError(UnexpectedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnexpectedToken(
            token: $token->getValue(),
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface In case of source content reading error.
     */
    private function unrecognizedTokenError(UnrecognizedTokenException $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedToken(
            token: $token->getValue(),
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface In case of source content reading error.
     */
    private function semanticError(SemanticException $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromSemanticError(
            message: $e->getMessage(),
            statement: $source->getContents(),
            offset: $e->getOffset(),
            code: $e->getCode(),
        );
    }

    /**
     * @throws SourceExceptionInterface In case of source content reading error.
     */
    private function parserRuntimeError(ParserRuntimeExceptionInterface $e, ReadableInterface $source): ParseException
    {
        $token = $e->getToken();

        return ParseException::fromUnrecognizedSyntaxError(
            statement: $source->getContents(),
            offset: $token->getOffset(),
        );
    }

    /**
     * @throws SourceExceptionInterface In case of source content reading error.
     */
    private function internalError(\Throwable $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromInternalError(
            statement: $source->getContents(),
            e: $e
        );
    }
}
