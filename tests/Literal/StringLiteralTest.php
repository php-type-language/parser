<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\StringLiteralNode;

/**
 * Tests for the string literals the parser builds out of their raw
 * representation, including every escape sequence a double quoted one may
 * carry.
 */
final class StringLiteralTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    private function literal(string $code): StringLiteralNode
    {
        $node = $this->parse($code);

        self::assertInstanceOf(StringLiteralNode::class, $node);

        return $node;
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function provideDoubleQuotedStrings(): iterable
    {
        yield 'empty' => ['""', ''];
        yield 'plain' => ['"example"', 'example'];
        yield 'escaped quote' => ['"a\"b"', 'a"b'];
        yield 'newline' => ['"a\nb"', "a\nb"];
        yield 'carriage return' => ['"a\rb"', "a\rb"];
        yield 'tab' => ['"a\tb"', "a\tb"];
        yield 'vertical tab' => ['"a\vb"', "a\vb"];
        yield 'escape' => ['"a\eb"', "a\eb"];
        yield 'form feed' => ['"a\fb"', "a\fb"];
        yield 'dollar sign' => ['"a\$b"', 'a$b'];
        yield 'escaped backslash' => ['"a\\\\b"', 'a\\b'];
        yield 'hexadecimal sequence' => ['"\x41"', 'A'];
        yield 'uppercase hexadecimal sequence' => ['"\xFF"', "\xFF"];
        yield 'short hexadecimal sequence' => ['"\x9"', "\x09"];
        yield 'unicode sequence' => ['"\u{48}"', 'H'];
        yield 'multibyte unicode sequence' => ['"\u{1F600}"', "\u{1F600}"];
        yield 'unknown escape is kept as is' => ['"a\qb"', 'a\qb'];
        yield 'several sequences' => ['"\x41\u{42}\n"', "AB\n"];
        yield 'null byte hexadecimal sequence' => ['"\x00"', "\0"];
        yield 'null byte unicode sequence' => ['"a\u{0}b"', "a\0b"];
        yield 'binary sequence does not break the next one' => ['"\xFF\u{42}"', "\xFF" . 'B'];
        yield 'escaped backslash before escaped quote' => ['"a\\\\\"b"', 'a\"b'];
        yield 'escaped backslash before escape sequence' => ['"a\\\\nb"', 'a\nb'];
        yield 'octal sequence' => ['"\101"', 'A'];
        yield 'octal null byte' => ['"\0"', "\0"];
        yield 'octal overflow is truncated' => ['"\777"', "\xFF"];
        yield 'escaped backslash before octal sequence' => ['"a\\\\101b"', 'a\101b'];
        yield 'code point above the unicode range' => ['"\u{110000}"', "\u{FFFD}"];
    }

    /**
     * @param non-empty-string $literal
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('provideDoubleQuotedStrings')]
    public function doubleQuotedStringIsDecoded(string $literal, string $value): void
    {
        $node = $this->literal($literal);

        self::assertSame($value, $node->value);
        self::assertSame($literal, $node->raw);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function provideSingleQuotedStrings(): iterable
    {
        yield 'empty' => ["''", ''];
        yield 'plain' => ["'example'", 'example'];
        yield 'escaped quote' => ["'a\'b'", "a'b"];
        yield 'escaped backslash' => ["'a\\\\b'", 'a\\b'];
        yield 'two escaped backslashes' => ["'a\\\\\\\\b'", 'a\\\\b'];
        yield 'escaped quote behind an escaped backslash' => ["'a\\\\\\'b'", "a\\'b"];
        yield 'escape sequences are not decoded' => ["'a\\nb'", 'a\\nb'];
        yield 'hexadecimal sequence is not decoded' => ["'a\\x41b'", 'a\\x41b'];
        yield 'dollar sign is not decoded' => ["'a\$b'", 'a$b'];
    }

    /**
     * @param non-empty-string $literal
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('provideSingleQuotedStrings')]
    public function singleQuotedStringIsDecoded(string $literal, string $value): void
    {
        $node = $this->literal($literal);

        self::assertSame($value, $node->value);
        self::assertSame($literal, $node->raw);
    }

    /**
     * A node built by hand derives the raw representation from its value, and
     * reading that representation back gives the very same value.
     *
     * @throws \Throwable
     */
    #[Test]
    public function derivedRawValueCanBeParsedBack(): void
    {
        $node = new StringLiteralNode('a"b');

        self::assertSame($node->value, $this->literal($node->raw)->value);
    }
}
