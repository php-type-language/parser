<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\StringDecoder\UtfCharRenderer;
use TypeLang\Parser\Tests\TestCase;

final class UtfCharRendererTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{int<0, max>, string}>
     */
    public static function provideCodePoints(): iterable
    {
        yield 'null byte' => [0x00, "\0"];
        yield 'ascii' => [0x41, 'A'];
        yield 'last one byte char' => [0x7F, "\x7F"];
        yield 'first two bytes char' => [0x80, "\u{80}"];
        yield 'last two bytes char' => [0x7FF, "\u{7FF}"];
        yield 'first three bytes char' => [0x800, "\u{800}"];
        yield 'last three bytes char' => [0xFFFF, "\u{FFFF}"];
        yield 'first four bytes char' => [0x10000, "\u{10000}"];
        yield 'emoji' => [0x1F600, "\u{1F600}"];
        yield 'last unicode code point' => [0x10FFFF, "\u{10FFFF}"];
    }

    #[Test]
    #[DataProvider('provideCodePoints')]
    public function codePointIsEncoded(int $code, string $expected): void
    {
        self::assertSame($expected, UtfCharRenderer::render($code));
    }

    /**
     * @return iterable<non-empty-string, array{int<0, max>, int<1, 4>}>
     */
    public static function provideCodePointLengths(): iterable
    {
        yield 'one byte' => [0x7F, 1];
        yield 'two bytes' => [0x7FF, 2];
        yield 'three bytes' => [0xFFFF, 3];
        yield 'four bytes' => [0x10FFFF, 4];
    }

    #[Test]
    #[DataProvider('provideCodePointLengths')]
    public function encodedCharHasExpectedLength(int $code, int $length): void
    {
        self::assertSame($length, \strlen(UtfCharRenderer::render($code)));
    }

    /**
     * @return iterable<non-empty-string, array{int<0, max>}>
     */
    public static function provideOutOfRangeCodePoints(): iterable
    {
        yield 'first code point above the range' => [0x110000];
        yield 'huge code point' => [0xFFFFFFF];
        yield 'php int max' => [\PHP_INT_MAX];
    }

    #[Test]
    #[DataProvider('provideOutOfRangeCodePoints')]
    public function outOfRangeCodePointIsEncodedAsReplacementChar(int $code): void
    {
        self::assertSame("\u{FFFD}", UtfCharRenderer::render($code));
    }

    #[Test]
    public function everyEncodedCharIsAValidUtf8Sequence(): void
    {
        foreach ([0x00, 0x41, 0x7F, 0x80, 0x7FF, 0x800, 0xFFFF, 0x10000, 0x10FFFF] as $code) {
            self::assertTrue(
                \mb_check_encoding(UtfCharRenderer::render($code), 'UTF-8'),
                \sprintf('A code point 0x%X must be encoded as a valid utf-8 sequence', $code),
            );
        }
    }
}
