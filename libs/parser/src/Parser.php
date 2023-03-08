<?php

declare(strict_types=1);

namespace Hyper\Parser;

use Hyper\Parser\Exception\ParseException;
use Hyper\Parser\Node\Stmt\NamedType;
use Phplrt\Contracts\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\LexerInterface;
use Phplrt\Contracts\Parser\ParserInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\ContextInterface;
use Phplrt\Parser\Exception\UnexpectedTokenException;
use Phplrt\Parser\Exception\UnrecognizedTokenException;
use Phplrt\Parser\Grammar\RuleInterface;
use Phplrt\Parser\Parser as ParserCombinator;
use Phplrt\Parser\ParserConfigsInterface;
use Phplrt\Source\File;

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
    /**
     * @var non-empty-string
     */
    private const GRAMMAR_PATHNAME = __DIR__ . '/../resources/grammar.php';

    /**
     * @var ParserCombinator
     */
    private readonly ParserCombinator $parser;

    /**
     * @var Lexer
     */
    private readonly Lexer $lexer;

    public function __construct()
    {
        /** @var GrammarConfigArray $grammar */
        $grammar = require self::GRAMMAR_PATHNAME;

        $this->lexer = $this->createLexer($grammar);
        $this->parser = $this->createParser($this->lexer, $grammar);
    }

    /**
     * @param LexerInterface $lexer
     * @param GrammarConfigArray $grammar
     *
     * @return ParserCombinator
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
     * @param GrammarConfigArray $grammar
     *
     * @return Lexer
     */
    private function createLexer(array $grammar): Lexer
    {
        return new Lexer($grammar['tokens']['default'], $grammar['skip']);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return list<NamedType>
     * @throws ParseException
     */
    public function parse($source, array $options = []): iterable
    {
        if (\is_string($source)) {
            $source = \trim($source);
        }

        $source = File::fromSources($source);

        try {
            return $this->parser->parse($source, $options);
        } catch (UnexpectedTokenException $e) {
            $token = $e->getToken();
            throw ParseException::fromUnexpectedToken(
                $token->getValue(),
                $source->getContents(),
                $token->getOffset(),
            );
        } catch (UnrecognizedTokenException $e) {
            $token = $e->getToken();
            throw ParseException::fromUnrecognizedToken(
                $token->getValue(),
                $source->getContents(),
                $token->getOffset(),
            );
        } catch (RuntimeExceptionInterface $e) {
            $token = $e->getToken();
            throw ParseException::fromUnrecognizedSyntaxError(
                $source->getContents(),
                $token->getOffset(),
            );
        } catch (\Error $e) {
            throw ParseException::fromTypeInstantiationError($source->getContents(), $e);
        }
    }
}
