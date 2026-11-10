<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Exception\SyntaxErrorException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
use TypeLang\Parser\Tests\TestCase;

#[CoversClass(UnrecognizedSyntaxException::class)]
final class UnrecognizedSyntaxExceptionTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function unrecognizedDataProvider(): iterable
    {
        yield 'a dash before a name' => ['-foo', '-foo'];
        yield 'a lone dot' => ['.', '.'];
        yield 'an unclosed string' => ['"abc', '"abc'];
        yield 'a trailing separator' => ['Foo\\', '\\'];
    }

    #[Test]
    #[DataProvider('unrecognizedDataProvider')]
    public function isASyntaxErrorException(string $source): void
    {
        self::assertInstanceOf(SyntaxErrorException::class, $this->parseFailure($source));
    }

    #[Test]
    #[DataProvider('unrecognizedDataProvider')]
    public function isNotAnUnexpectedTokenException(string $source): void
    {
        self::assertNotInstanceOf(UnexpectedTokenException::class, $this->parseFailure($source));
    }

    #[Test]
    #[DataProvider('unrecognizedDataProvider')]
    public function isAnUnrecognizedSyntaxException(string $source): void
    {
        self::assertInstanceOf(UnrecognizedSyntaxException::class, $this->parseFailure($source));
    }

    #[Test]
    #[DataProvider('unrecognizedDataProvider')]
    public function messageQuotesTheFragment(string $source, string $fragment): void
    {
        self::assertSame(
            \sprintf('Unrecognized syntax "%s"', \addcslashes($fragment, '"')),
            $this->parseFailure($source)->getMessage(),
        );
    }

    #[Test]
    public function rangeCoversTheFragment(): void
    {
        $range = $this->parseFailure('Foo\\')->range;

        self::assertSame(3, $range->offset);
        self::assertSame(1, $range->length);
    }
}
