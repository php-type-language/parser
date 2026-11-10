<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Every base of an integer is a token of its own (grammar/lexemes.pp3).
 */
final class IntegerTokensTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function singleTokenDataProvider(): iterable
    {
        yield 'zero' => ['0', SyntaxChecker::T_INT_OCT_LEGACY];
        yield 'decimal' => ['42', SyntaxChecker::T_INT_DEC];
        yield 'negative decimal' => ['-42', SyntaxChecker::T_INT_DEC];
        yield 'signed decimal' => ['+42', SyntaxChecker::T_INT_DEC];
        yield 'decimal with a space behind the sign' => ['- 42', SyntaxChecker::T_INT_DEC];
        yield 'hexadecimal with a space behind the sign' => ['- 0x1F', SyntaxChecker::T_INT_HEX];
        yield 'decimal with separators' => ['1_000_000', SyntaxChecker::T_INT_DEC];

        yield 'hexadecimal' => ['0x1F', SyntaxChecker::T_INT_HEX];
        yield 'hexadecimal in upper case' => ['0X1F', SyntaxChecker::T_INT_HEX];
        yield 'hexadecimal in mixed case' => ['0XDeaD', SyntaxChecker::T_INT_HEX];
        yield 'hexadecimal with separators' => ['0xDE_AD', SyntaxChecker::T_INT_HEX];
        yield 'negative hexadecimal' => ['-0x1F', SyntaxChecker::T_INT_HEX];
        yield 'signed hexadecimal' => ['+0x1F', SyntaxChecker::T_INT_HEX];

        yield 'binary' => ['0b101', SyntaxChecker::T_INT_BIN];
        yield 'binary in upper case' => ['0B101', SyntaxChecker::T_INT_BIN];
        yield 'binary with separators' => ['0b10_10', SyntaxChecker::T_INT_BIN];
        yield 'negative binary' => ['-0b101', SyntaxChecker::T_INT_BIN];

        yield 'octal' => ['0o17', SyntaxChecker::T_INT_OCT];
        yield 'octal in upper case' => ['0O17', SyntaxChecker::T_INT_OCT];
        yield 'octal with separators' => ['0o6_5_5', SyntaxChecker::T_INT_OCT];
        yield 'negative octal' => ['-0o17', SyntaxChecker::T_INT_OCT];

        yield 'legacy octal' => ['017', SyntaxChecker::T_INT_OCT_LEGACY];
        yield 'legacy octal of zeros' => ['00', SyntaxChecker::T_INT_OCT_LEGACY];
        yield 'legacy octal with a separator' => ['0_42', SyntaxChecker::T_INT_OCT_LEGACY];
        yield 'negative legacy octal' => ['-017', SyntaxChecker::T_INT_OCT_LEGACY];
    }

    #[DataProvider('singleTokenDataProvider')]
    public function testIntegerIsOneTokenOfItsBase(string $source, int $expected): void
    {
        self::assertSame([$expected], (new SyntaxChecker())->tokenize($source));
    }

    /**
     * A digit that is not of the base ends the integer, so the rest is another token.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<int>}>
     */
    public static function splitTokensDataProvider(): iterable
    {
        yield 'eight after a zero' => ['08', [SyntaxChecker::T_INT_OCT_LEGACY, SyntaxChecker::T_INT_DEC]];
        yield 'nine behind a legacy octal' => ['0129', [SyntaxChecker::T_INT_OCT_LEGACY, SyntaxChecker::T_INT_DEC]];
        yield 'two behind a binary' => ['0b102', [SyntaxChecker::T_INT_BIN, SyntaxChecker::T_INT_DEC]];
        yield 'eight behind a prefixed octal' => ['0o78', [SyntaxChecker::T_INT_OCT, SyntaxChecker::T_INT_DEC]];
        yield 'two integers of different bases' => ['0x1F 0b1', [SyntaxChecker::T_INT_HEX, SyntaxChecker::T_INT_BIN]];
    }

    /**
     * @param list<int> $expected
     */
    #[DataProvider('splitTokensDataProvider')]
    public function testDigitOutOfTheBaseEndsTheInteger(string $source, array $expected): void
    {
        self::assertSame($expected, (new SyntaxChecker())->tokenize($source));
    }
}
