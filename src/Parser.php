<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Exception\LogicException;
use TypeLang\Parser\Exception\ParseException;
use Phplrt\Contracts\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\ContextInterface;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserCombinator;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\File;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Stmt\Statement;

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

        $this->stringPool = new \WeakMap();
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
                ParserConfigsInterface::CONFIG_AST_BUILDER  => new Builder($grammar['reducers']),
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
     * @psalm-suppress ImplementedReturnTypeMismatch
     *
     * @throws ParseException
     */
    public function parse(mixed $source): ?Statement
    {
        $source = File::fromSources($source);

        $allowedNestingLevel = (int)\ini_get('xdebug.max_nesting_level');

        try {
            \ini_set('xdebug.max_nesting_level', -1);

            try {
                foreach ($this->parser->parse($source) as $node) {
                    /** @var Statement */
                    return $node;
                }

                return null;
            } catch (UnexpectedTokenException $e) {
                throw $this->unexpectedTokenError($e, $source);
            } catch (UnrecognizedTokenException $e) {
                throw $this->unrecognizedTokenError($e, $source);
            } catch (RuntimeExceptionInterface $e) {
                throw $this->runtimeError($e, $source);
            } catch (LogicException $e) {
                throw $this->logicError($e, $source);
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

    private function logicError(LogicException $e, ReadableInterface $source): ParseException
    {
        return ParseException::fromLogicError(
            $e->getMessage(),
            $source->getContents(),
            $e->getOffset(),
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
