<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * Tests for the integer literal grammar (decimal, binary, octal, hexadecimal).
 */
#[Group('unit'), Group('type-lang/parser')]
final class IntLiteralTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function validIntDataProvider(): iterable
    {
        yield 'zero' => ['0', 0];
        yield 'decimal' => ['42', 42];
        yield 'negative decimal' => ['-42', -42];
        yield 'signed decimal' => ['+42', 42];
        yield 'decimal with underscore' => ['1_000_000', 1000000];

        yield 'binary' => ['0b10101101', 173];
        yield 'binary with underscore' => ['0b10_10_11_01', 173];
        yield 'binary uppercase prefix' => ['0B1010', 10];
        yield 'negative binary' => ['-0b1010', -10];
        yield 'signed binary' => ['+0b1010', 10];

        yield 'octal' => ['0o42', 34];
        yield 'octal uppercase prefix' => ['0O42', 34];
        yield 'legacy octal' => ['042', 34];
        yield 'octal with underscore' => ['0o42_23', 2195];

        yield 'hexadecimal' => ['0xDEAD', 57005];
        yield 'hexadecimal mixed case' => ['0XDeaD', 57005];
        yield 'hexadecimal with underscore' => ['0xDEAD_BEEF', 3735928559];
        yield 'signed hexadecimal' => ['+0xDEAD', 57005];
        yield 'signed octal' => ['+0o42', 34];

        yield 'legacy octal of the zero alone' => ['00', 0];
        yield 'legacy octal is the prefixed one' => ['0123', 0o123];

        yield 'separated decimal' => ['42_04', 4204];
        yield 'separated legacy octal' => ['04_23', 275];
        yield 'separated legacy octal beside the zero' => ['0_42', 34];
        yield 'separated octal' => ['0o6_5_5', 429];
        yield 'separated hexadecimal' => ['0xFE_DE', 65246];
        yield 'separated binary' => ['0b0001_1000', 24];
    }

    /**
     * A separator is only allowed between two digits.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function invalidSeparatorDataProvider(): iterable
    {
        yield 'trailing' => ['42_', 'unexpected "_"'];
        yield 'doubled' => ['4__2', 'unexpected "__2"'];
        yield 'after the hexadecimal prefix' => ['0x_FF', 'unexpected "x_FF"'];
        yield 'after the binary prefix' => ['0b_1', 'unexpected "b_1"'];
        yield 'after the octal prefix' => ['0o_7', 'unexpected "o_7"'];
    }

    #[DataProvider('invalidSeparatorDataProvider')]
    public function testSeparatorBelongsBetweenDigits(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    #[DataProvider('validIntDataProvider')]
    public function testValidIntegers(string $type, int $expected): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(IntLiteralNode::class, $statement);
        self::assertSame($expected, $statement->value);
    }

    public function testBinaryAllowsOnlyZeroAndOne(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('0b101042');
    }

    public function testOctalAllowsOnlyDigitsUpToSeven(): void
    {
        $this->expectParsingException('unexpected "81"');

        $this->parse('0o4281');
    }

    /**
     * A leading zero means an octal, so a digit outside that radix cannot
     * follow one.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function leadingZeroDataProvider(): iterable
    {
        yield 'eight' => ['08', 'unexpected "8"'];
        yield 'nine' => ['09_1', 'unexpected "9_1"'];
    }

    #[DataProvider('leadingZeroDataProvider')]
    public function testDecimalCannotBeginWithZero(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
    }

    public function testHexadecimalAllowsOnlyHexDigits(): void
    {
        $this->expectParsingException();

        $this->parse('0xHELL');
    }
}
