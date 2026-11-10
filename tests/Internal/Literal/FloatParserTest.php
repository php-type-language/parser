<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\SubParser\FloatParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\FloatLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source.
 */
final class FloatParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, float}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'fraction' => ['1.5', 1.5];
        yield 'signed fraction' => ['+1.5', 1.5];
        yield 'negative fraction' => ['-1.5', -1.5];
        yield 'without a fraction' => ['1.', 1.0];
        yield 'without an integer part' => ['.5', 0.5];
        yield 'exponent' => ['1e3', 1000.0];
        yield 'exponent in upper case' => ['1E3', 1000.0];
        yield 'positive exponent' => ['1e+3', 1000.0];
        yield 'negative exponent' => ['1e-3', 0.001];
        yield 'fraction without digits and exponent' => ['1.e3', 1000.0];
        yield 'zero exponent' => ['0e0', 0.0];
        yield 'separators' => ['1_0.5_0', 10.5];
        yield 'separator in the exponent' => ['1e1_0', 1.0E+10];
        yield 'leading zero' => ['017.5', 17.5];
        yield 'smallest denormal' => ['4.9e-324', 4.9E-324];
        yield 'underflow' => ['1e-999', 0.0];
        yield 'overflow' => ['1e999', \PHP_FLOAT_MAX];
        yield 'negative overflow' => ['-1e999', -\PHP_FLOAT_MAX];
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueIsDecoded(string $raw, float $expected): void
    {
        $node = (new FloatParser())->parse($raw);

        self::assertSame($expected, $node->value);
    }

    /**
     * The exact number survives where the value cannot hold it.
     *
     * @return iterable<non-empty-string, array{non-empty-string, numeric-string, numeric-string}>
     */
    public static function exactDataProvider(): iterable
    {
        yield 'fraction' => ['1.5', '1.5', '0'];
        yield 'trailing zero' => ['0.10', '0.10', '0'];
        yield 'long fraction' => ['0.1000000000000000055511151231257827', '0.1000000000000000055511151231257827', '0'];
        yield 'without a fraction' => ['1.', '1', '0'];
        yield 'without an integer part' => ['.5', '0.5', '0'];
        yield 'negative without an integer part' => ['-.5', '-0.5', '0'];
        yield 'negative without a fraction' => ['-1.', '-1', '0'];
        yield 'zero fraction without an integer part' => ['.0', '0.0', '0'];
        yield 'leading zeros without a fraction' => ['007.', '7', '0'];
        yield 'without a fraction and with an exponent' => ['1.e3', '1', '3'];
        yield 'without an integer part and with an exponent' => ['.5e-2', '0.5', '-2'];
        yield 'zero fraction' => ['0.0', '0.0', '0'];
        yield 'leading zeros and zero fraction' => ['00.0', '0.0', '0'];
        yield 'exponent' => ['1e3', '1', '3'];
        yield 'signed exponent' => ['+1.5E+2', '1.5', '2'];
        yield 'negative' => ['-1.5e-2', '-1.5', '-2'];
        yield 'separators' => ['-1_0.5_0e-1_0', '-10.50', '-10'];
        yield 'leading zeros' => ['-055e45', '-55', '45'];
        yield 'leading zeros in the exponent' => ['1.5e-007', '1.5', '-7'];
        yield 'overflow' => ['1e999', '1', '999'];
        yield 'underflow' => ['1e-999', '1', '-999'];
        yield 'exponent in upper case' => ['1E3', '1', '3'];
        yield 'positive exponent' => ['1e+3', '1', '3'];
        yield 'signed' => ['+1.5', '1.5', '0'];
        yield 'leading zeros before the point' => ['00.5', '0.5', '0'];
        yield 'zero before the point' => ['0.5', '0.5', '0'];
        yield 'negative zero' => ['-0.0', '-0.0', '0'];
        yield 'zero exponent' => ['1e+00', '1', '0'];
        yield 'negative zero exponent' => ['1e-00', '1', '0'];
        yield 'exponent with trailing zeros' => ['1e-100', '1', '-100'];
        yield 'negative with a space' => ['- 1.5', '-1.5', '0'];
        yield 'positive with a space' => ['+ 1.5', '1.5', '0'];
        yield 'negative with a space and an exponent' => ['- 1.5e-2', '-1.5', '-2'];
        yield 'negative with a line break' => ["-\n1.5", '-1.5', '0'];
        yield 'negative with a tab and separators' => ["-\t1_0.5e+0_3", '-10.5', '3'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $mantissa
     * @param numeric-string $exponent
     */
    #[Test]
    #[DataProvider('exactDataProvider')]
    public function testMantissaAndExponentKeepTheExactNumber(string $raw, string $mantissa, string $exponent): void
    {
        $node = (new FloatParser())->parse($raw);

        self::assertSame($mantissa, $node->mantissa);
        self::assertSame($exponent, $node->exponent);
    }

    /**
     * @return iterable<non-empty-string, array{float}>
     */
    public static function derivedRawDataProvider(): iterable
    {
        yield 'whole number' => [1.0];
        yield 'fraction' => [0.1];
        yield 'negative' => [-1.5];
        yield 'large' => [1.0E+25];
        yield 'small' => [1.5E-10];
        yield 'epsilon' => [\PHP_FLOAT_EPSILON];
        yield 'maximum' => [\PHP_FLOAT_MAX];
        yield 'smallest denormal' => [4.9E-324];
    }

    #[Test]
    #[DataProvider('derivedRawDataProvider')]
    public function testRawDerivedByTheNodeIsDecodedBack(float $value): void
    {
        $raw = (new FloatLiteralNode($value))->raw;

        self::assertSame($value, (new FloatParser())->parse($raw)->value);
    }

    #[Test]
    public function testNegativeZeroKeepsItsSign(): void
    {
        $node = (new FloatParser())->parse('-0.0');

        self::assertSame(-\INF, \fdiv(1.0, $node->value));
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new FloatParser())->parse('-1_0.5e+1_0', 42);

        self::assertSame('-1_0.5e+1_0', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
