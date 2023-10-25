<?php

declare(strict_types=1);

namespace TypeLang\Parser;

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
     * @throws ParseException
     */
    public function parse(mixed $source): ?TypeStatement
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::fromSources($source);

        return $this->executeAndHandleErrors($source, 'TypeStatement');
    }

    /**
     * @param non-empty-literal-string $initial
     *
     * @throws ParseException
     */
    private function executeAndHandleErrors(ReadableInterface $source, string $initial): ?TypeStatement
    {
        $allowedNestingLevel = (int)\ini_get('xdebug.max_nesting_level');

        try {
            \ini_set('xdebug.max_nesting_level', -1);

            try {
                return $this->execute($source, $initial);
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

    /**
     * @param non-empty-string $initial
     *
     * @throws \Throwable
     */
    private function execute(ReadableInterface $source, string $initial): ?TypeStatement
    {
        $result = $this->parser
            ->startsAt($initial)
            ->parse($source);

        foreach ($result as $node) {
            /** @var TypeStatement */
            return $node;
        }

        return null;
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
