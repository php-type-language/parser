<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Lexer\LexerExceptionInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Lexer\LexerRuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Parser\ParserExceptionInterface;
use Phplrt\Contracts\Parser\ParserRuntimeExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Config\PassthroughHandler;
use Phplrt\Lexer\Lexer as LexerRuntime;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\Context;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserRuntime;
use Phplrt\Parser\ParserConfigsInterface;
use TypeLang\Parser\ParsedResult;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * @phpstan-type GrammarConfigArrayType array{
 *     initial: array-key,
 *     tokens: array{
 *         default: array<non-empty-string, non-empty-string>,
 *         ...
 *     },
 *     skip: list<non-empty-string>,
 *     grammar: array<array-key, RuleInterface>,
 *     reducers: array<int<0, max>|non-empty-string, callable(Context, mixed): mixed>,
 *     transitions?: array<array-key, mixed>
 * }
 * @phpstan-type ParserConfigArrayType array<ParserConfigsInterface::CONFIG_*, mixed>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 */
final readonly class ExecutionContext
{
    /**
     * @var ParserRuntime<TypeNode>
     */
    private ParserRuntime $parser;

    private LexerRuntime $lexer;

    /**
     * In-memory string literal pool.
     *
     * @api this property is accessible inside the grammar reducers
     *
     * @var \WeakMap<TokenInterface, StringLiteralNode>
     */
    protected \WeakMap $stringPool;

    /**
     * In-memory integer literal pool.
     *
     * @api this property is accessible inside the grammar reducers
     *
     * @var \WeakMap<TokenInterface, IntLiteralNode>
     */
    protected \WeakMap $integerPool;

    public function __construct(
        /**
         * @api this property is accessible inside the grammar reducers
         */
        protected TypeParserFeatures $features,
        /**
         * @api this property is accessible inside the grammar reducers
         */
        protected bool $tolerant,
    ) {
        /** @phpstan-var GrammarConfigArrayType $grammar */
        $grammar = require __DIR__ . '/../../resources/grammar.php';

        $this->stringPool = new \WeakMap();
        $this->integerPool = new \WeakMap();

        $this->lexer = $this->createLexer($grammar);
        $this->parser = $this->createParser($this->lexer, $grammar);
    }

    /**
     * @param array<int<0, max>|non-empty-string, callable(Context, mixed):mixed> $reducers
     */
    private function createBuilder(array $reducers): BuilderInterface
    {
        return new NodeBuilder($reducers);
    }

    /**
     * @param GrammarConfigArrayType $grammar
     * @return ParserRuntime<TypeNode>
     */
    private function createParser(LexerInterface $lexer, array $grammar): ParserRuntime
    {
        /** @var ParserRuntime<TypeNode> */
        return new ParserRuntime(
            lexer: $lexer,
            grammar: $grammar['grammar'],
            options: $this->createParserOptions($grammar),
        );
    }

    /**
     * @param GrammarConfigArrayType $grammar
     * @return ParserConfigArrayType
     */
    private function createParserOptions(array $grammar): array
    {
        return [
            ParserConfigsInterface::CONFIG_INITIAL_RULE => $grammar['initial'],
            ParserConfigsInterface::CONFIG_ALLOW_TRAILING_TOKENS => $this->tolerant,
            ParserConfigsInterface::CONFIG_AST_BUILDER => $this->createBuilder($grammar['reducers']),
        ];
    }

    /**
     * @param GrammarConfigArrayType $grammar
     */
    private function createLexer(array $grammar): LexerRuntime
    {
        return new LexerRuntime(
            tokens: $grammar['tokens']['default'],
            skip: $grammar['skip'],
            onUnknownToken: new PassthroughHandler(),
        );
    }

    /**
     * @return iterable<array-key, TokenInterface>
     * @throws LexerExceptionInterface
     * @throws LexerRuntimeExceptionInterface
     */
    public function lex(ReadableInterface $source): iterable
    {
        return $this->lexer->lex($source);
    }

    /**
     * @throws ParserRuntimeExceptionInterface
     * @throws ParserExceptionInterface
     */
    public function parse(ReadableInterface $source): ?ParsedResult
    {
        foreach ($this->parser->parse($source) as $stmt) {
            $offset = 0;
            $context = $this->parser->getLastExecutionContext();

            if ($context !== null) {
                $token = $context->buffer->current();
                $offset = $token->getOffset();
            }

            return new ParsedResult($stmt, $offset);
        }

        return null;
    }
}
