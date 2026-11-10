<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Exception\MalformedSyntaxException;
use TypeLang\Parser\Exception\SyntaxErrorException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Tests\TestCase;

#[CoversClass(UnexpectedTokenException::class)]
final class UnexpectedTokenExceptionTest extends TestCase
{
    #[Test]
    public function isASyntaxErrorException(): void
    {
        self::assertInstanceOf(SyntaxErrorException::class, $this->parseFailure('int string'));
    }

    #[Test]
    public function isNotAMalformedSyntaxException(): void
    {
        self::assertNotInstanceOf(MalformedSyntaxException::class, $this->parseFailure('int string'));
    }

    #[Test]
    public function messageNamesTheTokenAndTheExpectedOnes(): void
    {
        self::assertSame(
            'Unexpected "string"',
            $this->parseFailure('int string')->getMessage(),
        );
    }

    #[Test]
    public function rangeCoversTheToken(): void
    {
        $range = $this->parseFailure('int string')->range;

        self::assertSame(4, $range->offset);
        self::assertSame(6, $range->length);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function trailingTokenDataProvider(): iterable
    {
        yield 'a name after a name' => ['int string', 'Unexpected "string"'];
        yield 'a closing parenthesis' => ['int)', 'Unexpected ")"'];
        yield 'a question mark after a type' => ['int?', 'Unexpected "?"'];
        yield 'a tail after a shape' => ['array{a: int} tail', 'Unexpected "tail"'];
        yield 'a doubled closing angle' => ['Foo<int>>', 'Unexpected ">"'];
        yield 'a doubled mask' => ['Foo::**', 'Unexpected "*"'];
    }

    #[Test]
    #[DataProvider('trailingTokenDataProvider')]
    public function nothingIsExpectedAfterACompleteType(string $source, string $message): void
    {
        self::assertSame($message, $this->parseFailure($source)->getMessage());
    }

    #[Test]
    public function expectedTokensAreListedInsideAnIncompleteType(): void
    {
        self::assertSame(
            'Unexpected end of input, expected a type name, an identifier, "true", "false" and other',
            $this->parseFailure('int|')->getMessage(),
        );
    }

    #[Test]
    public function keepsTheSource(): void
    {
        self::assertSame('int string', $this->parseFailure('int string')->source->content);
    }

    #[Test]
    public function endOfInputIsReportedByTheSameException(): void
    {
        $e = $this->parseFailure('int|');

        self::assertInstanceOf(UnexpectedTokenException::class, $e);
        self::assertStringStartsWith('Unexpected end of input, expected ', $e->getMessage());
    }

    #[Test]
    public function endOfInputRangeIsEmptyAndStandsAtTheEnd(): void
    {
        $range = $this->parseFailure('int|')->range;

        self::assertSame(4, $range->offset);
        self::assertSame(0, $range->length);
    }

    #[Test]
    public function longExpectationsAreCut(): void
    {
        self::assertStringEndsWith(' and other', $this->parseFailure('')->getMessage());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function plainFailureDataProvider(): iterable
    {
        yield 'a name after a name' => ['int string'];
        yield 'a dangling union' => ['int|'];
        yield 'an extra closing token' => ['int)'];
        yield 'a stray comma' => [','];
    }

    #[Test]
    #[DataProvider('plainFailureDataProvider')]
    public function plainFailureIsNeverMalformed(string $source): void
    {
        self::assertNotInstanceOf(MalformedSyntaxException::class, $this->parseFailure($source));
    }
}
