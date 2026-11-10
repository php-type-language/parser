<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\SubParser\DqStringParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for the ones in {@see lenientDataProvider()} and the lack of
 * interpolation.
 */
final class DqStringParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'empty' => ['""', ''];
        yield 'plain' => ['"abc"', 'abc'];
        yield 'multibyte' => ['"é😀"', 'é😀'];
        yield 'single quote' => ['"\'"', "'"];
        yield 'line break' => ["\"a\nb\"", "a\nb"];

        yield 'line feed' => ['"\n"', "\n"];
        yield 'carriage return' => ['"\r"', "\r"];
        yield 'tab' => ['"\t"', "\t"];
        yield 'vertical tab' => ['"\v"', "\v"];
        yield 'escape' => ['"\e"', "\e"];
        yield 'form feed' => ['"\f"', "\f"];
        yield 'dollar' => ['"\$"', '$'];
        yield 'backslash' => ['"\\\\"', '\\'];
        yield 'quote' => ['"\""', '"'];
        yield 'backslash and quote' => ['"\\\\\""', '\\"'];
        yield 'unknown sequence' => ['"\q"', '\q'];
        yield 'single quote sequence' => ['"\\\'"', "\\'"];
        yield 'backslash before a line break' => ["\"a\\\nb\"", "a\\\nb"];

        yield 'hexadecimal' => ['"\x41"', 'A'];
        yield 'hexadecimal in upper case' => ['"\X41"', 'A'];
        yield 'short hexadecimal' => ['"\x4"', "\x04"];
        yield 'long hexadecimal' => ['"\x414"', 'A4'];
        yield 'hexadecimal without digits' => ['"\xG"', '\xG'];
        yield 'escaped hexadecimal' => ['"\\\\x41"', '\x41'];
        yield 'escaped backslash before hexadecimal' => ['"\\\\\x41"', '\A'];

        yield 'octal' => ['"\101"', 'A'];
        yield 'short octal' => ['"\0"', "\0"];
        yield 'long octal' => ['"\1234"', 'S4'];
        yield 'octal overflow' => ['"\400"', "\0"];
        yield 'octal overflow with a digit' => ['"\777"', "\xFF"];
        yield 'not an octal digit' => ['"\8"', '\8'];

        yield 'unicode' => ['"\u{41}"', 'A'];
        yield 'unicode in lower case' => ['"\u{1f600}"', '😀'];
        yield 'unicode with leading zeros' => ['"\u{0041}"', 'A'];
        yield 'unicode null' => ['"\u{0}"', "\0"];
        yield 'unicode two bytes' => ['"\u{E9}"', 'é'];
        yield 'unicode three bytes' => ['"\u{20AC}"', '€'];
        yield 'unicode last code point' => ['"\u{10FFFF}"', "\xF4\x8F\xBF\xBF"];
        yield 'unicode surrogate' => ['"\u{D800}"', "\xED\xA0\x80"];
        yield 'unicode without braces' => ['"\u41"', '\u41'];
        yield 'unicode letter' => ['"\u"', '\u'];

        yield 'two sequences' => ['"\x41\102\u{43}"', 'ABC'];
        yield 'sequences between text' => ['"a\nb\tc"', "a\nb\tc"];
    }

    /**
     * Unlike PHP, a variable in the string is a part of it.
     *
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function interpolationDataProvider(): iterable
    {
        yield 'variable' => ['"$a"', '$a'];
        yield 'variable in braces' => ['"{$a}"', '{$a}'];
        yield 'variable in a text' => ['"a $b c"', 'a $b c'];
    }

    /**
     * PHP rejects each of these strings, the parser reads a sequence it
     * cannot decode as a text.
     *
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function lenientDataProvider(): iterable
    {
        yield 'unicode above the last code point' => ['"\u{110000}"', "\u{FFFD}"];
        yield 'unicode without closing brace' => ['"\u{"', '\u{'];
        yield 'unicode without digits' => ['"\u{}"', '\u{}'];
        yield 'unicode with a letter out of digits' => ['"\u{ZZ}"', '\u{ZZ}'];
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    #[DataProvider('interpolationDataProvider')]
    #[DataProvider('lenientDataProvider')]
    public function testValueIsDecoded(string $raw, string $expected): void
    {
        $node = (new DqStringParser())->parse($raw);

        self::assertSame($expected, $node->value);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function derivedRawDataProvider(): iterable
    {
        yield 'plain' => ['example'];
        yield 'empty' => [''];
        yield 'single quote' => ["a'b"];
        yield 'double quote' => ['a"b'];
        yield 'backslash' => ['a\\b'];
        yield 'escaped letter' => ['a\\nb'];
        yield 'line break' => ["a\nb"];
        yield 'carriage return' => ["a\r\nb"];
        yield 'tab' => ["a\tb"];
        yield 'null byte' => ["a\0b"];
        yield 'escape' => ["\e"];
        yield 'delete' => ["\x7f"];
        yield 'multibyte' => ['é😀'];
        yield 'dollar' => ['$a'];
    }

    #[Test]
    #[DataProvider('derivedRawDataProvider')]
    public function testRawDerivedByTheNodeIsDecodedBack(string $value): void
    {
        $raw = (new StringLiteralNode($value, StringLiteralKind::DoubleQuoted))->raw;
        $node = (new DqStringParser())->parse($raw);

        self::assertSame($value, $node->value);
    }

    #[Test]
    public function testRawKindAndOffsetAreKept(): void
    {
        $node = (new DqStringParser())->parse('"a\n"', 42);

        self::assertSame('"a\n"', $node->raw);
        self::assertSame(StringLiteralKind::DoubleQuoted, $node->kind);
        self::assertSame(42, $node->offset);
    }
}
