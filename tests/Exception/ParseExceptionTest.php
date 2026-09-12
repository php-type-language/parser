<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\Test;
use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Lexer\ChannelInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Token\Token;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\InternalParseException;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnreadableSourceException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
use TypeLang\Parser\Exception\UnrecognizedTokenException;
use TypeLang\Parser\Tests\TestCase;

final class ParseExceptionTest extends TestCase
{
    private static function source(string $statement): ReadableInterface
    {
        return SourceFactory::createDefault()->create($statement);
    }

    /**
     * @param int<0, max> $offset
     * @param non-empty-string|null $name
     */
    private static function token(
        string $value,
        int $offset = 0,
        ?string $name = 'T_NAME',
        ChannelInterface $channel = Channel::Default,
    ): TokenInterface {
        return new Token(
            id: 0,
            name: $name,
            channel: $channel,
            value: $value,
            offset: $offset,
        );
    }

    private static function unexpected(string $statement, string $message = 'Syntax error'): UnexpectedTokenException
    {
        return UnexpectedTokenException::becauseTokenIsUnexpected(
            message: $message,
            source: self::source($statement),
            token: self::token('foo', 4),
        );
    }

    #[Test]
    public function everyParseExceptionIsALogicError(): void
    {
        $exception = InternalParseException::becauseInternalErrorOccurs(
            self::source('int'),
            new \LogicException(),
        );

        self::assertInstanceOf(ParserException::class, $exception);
        self::assertInstanceOf(\LogicException::class, $exception);
    }

    #[Test]
    public function theMessageCarriesTheSourceButNoPlace(): void
    {
        $source = self::source('int|foo');
        $token = self::token('foo', 4);

        $exception = UnexpectedTokenException::becauseTokenIsUnexpected(
            message: 'Syntax error, unexpected "foo" (T_NAME)',
            source: $source,
            token: $token,
        );

        self::assertSame(
            'Syntax error, unexpected "foo" (T_NAME) in "int|foo"',
            $exception->getMessage(),
        );
        self::assertSame($source, $exception->source);
        self::assertSame($token, $exception->token);
    }

    #[Test]
    public function theRenderedErrorCarriesTheLocation(): void
    {
        $exception = self::unexpected('int|foo');

        self::assertStringContainsString(
            'Syntax error in "int|foo" on line 1 at column 5',
            (string) $exception,
        );
    }

    #[Test]
    public function thePrintedErrorIsTheOnePhpPrints(): void
    {
        $exception = self::unexpected('int|foo');

        $printed = (string) $exception;

        self::assertStringStartsWith(UnexpectedTokenException::class, $printed);
        self::assertStringContainsString('Syntax error in "int|foo"', $printed);
        self::assertStringContainsString('Stack trace:', $printed);
        self::assertStringContainsString($exception->getFile(), $printed);
    }

    #[Test]
    public function thePrintedErrorLeavesTheMessageAlone(): void
    {
        $exception = self::unexpected('int|foo');

        $printed = (string) $exception;

        self::assertSame('Syntax error in "int|foo"', $exception->getMessage());
        self::assertSame($printed, (string) $exception);
    }

    #[Test]
    public function theMultilineStatementIsReportedUsingTheLineAndColumn(): void
    {
        $exception = UnexpectedTokenException::becauseTokenIsUnexpected(
            message: 'Syntax error',
            source: self::source("int|\nx"),
            token: self::token('x', 5),
        );

        self::assertStringContainsString('on line 2 at column 1', (string) $exception);
    }

    #[Test]
    public function theLongStatementIsTruncated(): void
    {
        $statement = \str_repeat('x', 100) . 'foo';
        $exception = self::unexpected($statement);

        self::assertStringContainsString('…', (string) $exception);
        self::assertStringNotContainsString($statement, (string) $exception);
    }

    #[Test]
    public function theUnrecognizedTokenIsReportedByItsValue(): void
    {
        $exception = UnrecognizedTokenException::becauseTokenIsUnrecognized(
            self::source('int|%'),
            self::token('%', 4, null, Channel::Unknown),
        );

        self::assertSame(
            'Syntax error, unexpected "%" (unknown token) in "int|%"',
            $exception->getMessage(),
        );
        self::assertStringContainsString('on line 1 at column 5', (string) $exception);
    }

    #[Test]
    public function theEndOfInputIsReportedInsteadOfAToken(): void
    {
        $exception = UnrecognizedTokenException::becauseTokenIsUnrecognized(
            self::source('int|'),
            self::token('', 4, null, Channel::EndOfInput),
        );

        self::assertStringStartsWith('Syntax error, unexpected end of input', $exception->getMessage());
    }

    #[Test]
    public function theUnrecognizedSyntaxIsReportedWithItsLocation(): void
    {
        $exception = UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized(
            self::source('int|'),
            self::token('', 4, null, Channel::EndOfInput),
        );

        self::assertSame('Internal syntax error in "int|"', $exception->getMessage());
        self::assertStringContainsString('on line 1 at column 5', (string) $exception);
    }

    #[Test]
    public function theBlankStatementIsReportedAsEmpty(): void
    {
        $exception = UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized(
            self::source('   '),
            self::token('', 0, null, Channel::EndOfInput),
        );

        self::assertStringContainsString('<empty statement>', (string) $exception);
    }

    #[Test]
    public function theInternalErrorKeepsThePreviousException(): void
    {
        $previous = new \LogicException('oops');
        $exception = InternalParseException::becauseInternalErrorOccurs(self::source('int'), $previous);

        self::assertSame('An internal error occurred while parsing "int"', $exception->getMessage());
        self::assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function theUnreadableSourceIsReportedUsingTheOriginalMessage(): void
    {
        $previous = new class ('source is unreadable') extends \RuntimeException implements
            \Phplrt\Contracts\Source\Exception\SourceExceptionInterface {};

        $source = self::source('int');
        $exception = UnreadableSourceException::becauseSourceIsUnreadable($source, $previous);

        self::assertInstanceOf(ParserException::class, $exception);
        self::assertSame('source is unreadable', $exception->getMessage());
        self::assertSame($source, $exception->source);
        self::assertSame($previous, $exception->getPrevious());
    }

    /**
     * Every error is told apart by the class it is of, so none of them carries
     * a code.
     */
    #[Test]
    public function everyErrorIsToldApartByItsClassAlone(): void
    {
        self::assertSame(0, self::unexpected('int|foo')->getCode());
        self::assertSame(0, InternalParseException::becauseInternalErrorOccurs(
            self::source('int'),
            new \LogicException(),
        )->getCode());
    }
}
