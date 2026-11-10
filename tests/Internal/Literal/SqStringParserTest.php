<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\SubParser\SqStringParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source.
 */
final class SqStringParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'empty' => ["''", ''];
        yield 'plain' => ["'abc'", 'abc'];
        yield 'multibyte' => ["'é😀'", 'é😀'];
        yield 'double quote' => ["'\"'", '"'];
        yield 'line break' => ["'a\nb'", "a\nb"];

        yield 'quote' => ["'\\''", "'"];
        yield 'backslash' => ["'\\\\'", '\\'];
        yield 'text before a backslash' => ["'a\\\\'", 'a\\'];
        yield 'backslash and quote' => ["'\\\\\\''", "\\'"];
        yield 'quote and backslash' => ["'\\'\\\\'", "'\\"];
        yield 'two backslashes' => ["'\\\\\\\\'", '\\\\'];
        yield 'lone backslash before a letter' => ["'\\n'", '\n'];
        yield 'backslash before a line break' => ["'a\\\nb'", "a\\\nb"];

        yield 'hexadecimal is not decoded' => ["'\\x41'", '\x41'];
        yield 'octal is not decoded' => ["'\\101'", '\101'];
        yield 'unicode is not decoded' => ["'\\u{41}'", '\u{41}'];
        yield 'dollar is not decoded' => ["'\\\$a'", '\$a'];
        yield 'variable' => ["'\$a'", '$a'];
        yield 'escaped double quote' => ["'\\\"'", '\"'];
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueIsDecoded(string $raw, string $expected): void
    {
        $node = (new SqStringParser())->parse($raw);

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
        $raw = (new StringLiteralNode($value, StringLiteralKind::SingleQuoted))->raw;
        $node = (new SqStringParser())->parse($raw);

        self::assertSame($value, $node->value);
    }

    #[Test]
    public function testRawKindAndOffsetAreKept(): void
    {
        $node = (new SqStringParser())->parse("'a\\''", 42);

        self::assertSame("'a\\''", $node->raw);
        self::assertSame(StringLiteralKind::SingleQuoted, $node->kind);
        self::assertSame(42, $node->offset);
    }
}
