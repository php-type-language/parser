<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Numbers, strings, `true`, `false` and `null` (grammar/literals.pp3).
 */
final class LiteralsTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validLiteralDataProvider(): iterable
    {
        yield 'zero' => ['0'];
        yield 'decimal' => ['42'];
        yield 'negative decimal' => ['-42'];
        yield 'signed decimal' => ['+42'];
        yield 'space behind the sign' => ['- 1'];
        yield 'space behind the plus' => ['+ 1'];
        yield 'several spaces behind the sign' => ['-   42'];
        yield 'tab behind the sign' => ["-\t42"];
        yield 'line break behind the sign' => ["-\n42"];
        yield 'space and line break behind the sign' => ["- \r\n 42"];
        yield 'space behind the sign of a hexadecimal' => ['- 0x1F'];
        yield 'space behind the sign of a binary' => ['- 0b101'];
        yield 'space behind the sign of an octal' => ['- 0o17'];
        yield 'space behind the sign of a legacy octal' => ['- 017'];
        yield 'space behind the sign of a float' => ['- 1.5'];
        yield 'decimal with separators' => ['1_000_000'];
        yield 'binary' => ['0b101'];
        yield 'binary in upper case' => ['0B101'];
        yield 'binary with separators' => ['0b10_10'];
        yield 'octal' => ['0o17'];
        yield 'octal in upper case' => ['0O17'];
        yield 'legacy octal' => ['017'];
        yield 'legacy octal of zeros' => ['00'];
        yield 'legacy octal with a separator' => ['0_42'];
        yield 'hexadecimal' => ['0x1F'];
        yield 'hexadecimal in mixed case' => ['0XDeaD'];
        yield 'hexadecimal with separators' => ['0xDE_AD'];
        yield 'negative hexadecimal' => ['-0x1F'];

        yield 'float' => ['1.5'];
        yield 'zero float' => ['0.0'];
        yield 'negative float' => ['-1.5'];
        yield 'float without an integer part' => ['.5'];
        yield 'negative float without an integer part' => ['-.5'];
        yield 'float without a fraction' => ['1.'];
        yield 'float with an exponent' => ['1e3'];
        yield 'float with an upper case exponent' => ['1E3'];
        yield 'float with a signed exponent' => ['1.5e-3'];
        yield 'float with a positive exponent' => ['1.5e+3'];
        yield 'float with separators' => ['1_000.5'];
        yield 'float with a leading zero' => ['08.5'];
        yield 'float with a leading zero and an exponent' => ['01e3'];
        yield 'float with a dot and an exponent' => ['1.e3'];

        yield 'single quoted string' => ["'a'"];
        yield 'double quoted string' => ['"a"'];
        yield 'empty single quoted string' => ["''"];
        yield 'empty double quoted string' => ['""'];
        yield 'string with spaces' => ["'a b'"];
        yield 'escaped quote' => ["'it\\'s'"];
        yield 'escaped double quote' => ['"say \\"hi\\""'];
        yield 'escaped backslash' => ["'a\\\\b'"];
        yield 'other quote inside' => ["\"it's\""];
        yield 'syntax inside' => ["'array{a: int}'"];
        yield 'unicode' => ["'Фу 😀'"];
        yield 'escape sequences' => ['"a\\n\\x41\\u{1F600}\\101"'];
        yield 'line break inside a single quoted string' => ["'a\nb'"];
        yield 'line break inside a double quoted string' => ["\"a\nb\""];
        yield 'carriage return and line break inside a string' => ["'a\r\nb'"];
        yield 'several lines in a string' => ["\"first\nsecond\n\nthird\""];

        yield 'true' => ['true'];
        yield 'false' => ['false'];
        yield 'null' => ['null'];
        yield 'true in upper case' => ['TRUE'];
        yield 'false in mixed case' => ['False'];
        yield 'null in mixed case' => ['Null'];
    }

    #[DataProvider('validLiteralDataProvider')]
    public function testLiteralIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidLiteralDataProvider(): iterable
    {
        yield 'double sign' => ['--1'];
        yield 'hexadecimal without digits' => ['0x'];
        yield 'hexadecimal with a letter out of digits' => ['0xG'];
        yield 'binary with a wrong digit' => ['0b2'];
        yield 'binary with a wrong digit behind' => ['0b102'];
        yield 'octal with a wrong digit' => ['0o8'];
        yield 'legacy octal with a wrong digit' => ['08'];
        yield 'legacy octal with a wrong digit behind' => ['0129'];
        yield 'separator behind the dot' => ['1._5'];
        yield 'separator behind the hexadecimal prefix' => ['0x_1'];
        yield 'separator behind the binary prefix' => ['0b_1'];
        yield 'separator behind the octal prefix' => ['0o_7'];
        yield 'trailing separator' => ['1_'];
        yield 'double separator' => ['1__000'];
        yield 'separator before the dot' => ['1_.5'];
        yield 'exponent without digits' => ['1e'];
        yield 'signed exponent without digits' => ['1e+'];
        yield 'two dots' => ['1.5.5'];
        yield 'fraction in an exponent' => ['1e3.5'];
        yield 'space inside' => ['1 . 5'];
        yield 'negative infinity' => ['-INF'];

        yield 'unterminated single quoted string' => ["'a"];
        yield 'unterminated double quoted string' => ['"a'];
        yield 'unterminated escape' => ["'a\\"];
        yield 'string and a name' => ["'a'b"];
        yield 'two strings glued' => ["'a''b'"];
        yield 'two strings' => ["'a' 'b'"];
    }

    #[DataProvider('invalidLiteralDataProvider')]
    public function testLiteralIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
