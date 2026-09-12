<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\StringDecoder;
use TypeLang\Parser\Tests\TestCase;

final class StringParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{string, string}>
     */
    public static function provideBodies(): iterable
    {
        yield 'empty' => ['', ''];
        yield 'without sequences' => ['example', 'example'];
        yield 'line feed' => ['a\nb', "a\nb"];
        yield 'carriage return' => ['a\rb', "a\rb"];
        yield 'tab' => ['a\tb', "a\tb"];
        yield 'vertical tab' => ['a\vb', "a\vb"];
        yield 'escape' => ['a\eb', "a\eb"];
        yield 'form feed' => ['a\fb', "a\fb"];
        yield 'dollar sign' => ['a\$b', 'a$b'];
        yield 'double quote' => ['a"b', 'a"b'];
        yield 'double quote behind an escaped backslash' => ['a\\\\"b', 'a\\"b'];
        yield 'backslash' => ['a\\\\b', 'a\\b'];
        yield 'backslash before escape sequence' => ['a\\\\nb', 'a\nb'];
        yield 'two backslashes' => ['a\\\\\\\\b', 'a\\\\b'];
        yield 'hexadecimal' => ['\x41', 'A'];
        yield 'uppercase hexadecimal prefix' => ['\X41', 'A'];
        yield 'short hexadecimal' => ['\x9', "\x09"];
        yield 'null byte' => ['\x00', "\0"];
        yield 'unicode' => ['\u{48}', 'H'];
        yield 'multibyte unicode' => ['\u{1F600}', "\u{1F600}"];
        yield 'unicode null byte' => ['a\u{0}b', "a\0b"];
        yield 'binary sequence before unicode' => ['\xFF\u{42}', "\xFF" . 'B'];
        yield 'backslash before hexadecimal' => ['a\\\\x41b', 'a\x41b'];
        yield 'octal' => ['\101', 'A'];
        yield 'octal null byte' => ['\0', "\0"];
        yield 'octal overflow is truncated' => ['\777', "\xFF"];
        yield 'backslash before octal' => ['a\\\\101b', 'a\101b'];
        yield 'code point above the unicode range' => ['\u{110000}', "\u{FFFD}"];
        yield 'huge code point' => ['\u{FFFFFFF}', "\u{FFFD}"];
        yield 'last unicode code point' => ['\u{10FFFF}', "\u{10FFFF}"];
        yield 'unknown sequence' => ['a\qb', 'a\qb'];
        yield 'incomplete unicode sequence' => ['a\u{}b', 'a\u{}b'];
        yield 'trailing backslash' => ['a\\', 'a\\'];
        yield 'repeated sequences' => ['\x41\x41\u{42}\u{42}', 'AABB'];
        yield 'several kinds at once' => ['\x41\u{42}\n\\\\', "AB\n" . '\\'];
    }

    #[Test]
    #[DataProvider('provideBodies')]
    public function bodyIsDecoded(string $body, string $expected): void
    {
        self::assertSame($expected, StringDecoder::decode($body, true));
    }

    #[Test]
    public function rawNullByteIsNotTouched(): void
    {
        self::assertSame("a\0b", StringDecoder::decode("a\0b", true));
        self::assertSame("a\0\\b", StringDecoder::decode("a\0" . '\\\\b', true));
    }

    #[Test]
    public function invalidUtf8IsNotTouched(): void
    {
        self::assertSame("\xFF\xFE", StringDecoder::decode("\xFF\xFE", true));
    }

    #[Test]
    public function decodingIsIdempotentForStringsWithoutBackslashes(): void
    {
        $decoded = StringDecoder::decode('a\nb', true);

        self::assertSame($decoded, StringDecoder::decode($decoded, true));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function provideDoubleQuotedStrings(): iterable
    {
        yield 'empty' => ['""', ''];
        yield 'without sequences' => ['"example"', 'example'];
        yield 'quote' => ['"a\"b"', 'a"b'];
        yield 'sequence is left alone' => ['"a\nb"', 'a\nb'];
        yield 'backslash is left alone' => ['"a\\\\b"', 'a\\\\b'];
        yield 'quote behind an escaped backslash' => ['"a\\\\\"b"', 'a\\\\"b'];
        yield 'quote beside a sequence' => ['"asd\"\nasd"', 'asd"\nasd'];
    }

    #[Test]
    #[DataProvider('provideDoubleQuotedStrings')]
    public function doubleQuotedStringIsUnpacked(string $value, string $expected): void
    {
        self::assertSame($expected, StringDecoder::unpack($value, true));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function provideSingleQuotedStrings(): iterable
    {
        yield 'empty' => ["''", ''];
        yield 'without sequences' => ["'example'", 'example'];
        yield 'quote' => ["'a\\'b'", "a'b"];
        yield 'backslash is left alone' => ["'a\\\\b'", 'a\\\\b'];
        yield 'sequence is left alone' => ["'a\\nb'", 'a\nb'];
        yield 'quote behind an escaped backslash' => ["'a\\\\\\'b'", "a\\\\'b"];
    }

    #[Test]
    #[DataProvider('provideSingleQuotedStrings')]
    public function singleQuotedStringIsUnpacked(string $value, string $expected): void
    {
        self::assertSame($expected, StringDecoder::unpack($value, false));
    }
}
