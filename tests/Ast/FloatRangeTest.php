<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\FloatLiteralNode;

/**
 * A float out of the range of a native float keeps the nearest bound as its
 * value, never an infinity, and the exact digits in the mantissa and the
 * exponent.
 */
final class FloatRangeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, float, non-empty-string, non-empty-string}>
     */
    public static function overflowDataProvider(): iterable
    {
        // source, value, mantissa, exponent
        yield 'positive' => ['1e400', \PHP_FLOAT_MAX, '1', '400'];
        yield 'negative' => ['-1e400', -\PHP_FLOAT_MAX, '-1', '400'];
        yield 'just out of the range' => ['1e309', \PHP_FLOAT_MAX, '1', '309'];
        yield 'with a plus' => ['+1e400', \PHP_FLOAT_MAX, '1', '400'];
        yield 'with a plus and a space' => ['+ 1e400', \PHP_FLOAT_MAX, '1', '400'];
        yield 'with a minus and a space' => ['- 1e400', -\PHP_FLOAT_MAX, '-1', '400'];
        yield 'with a fraction' => ['1.5e400', \PHP_FLOAT_MAX, '1.5', '400'];
        yield 'with a signed exponent' => ['1.5e+400', \PHP_FLOAT_MAX, '1.5', '400'];
        yield 'with separators' => ['1_0e4_00', \PHP_FLOAT_MAX, '10', '400'];
        yield 'long integer part' => [\str_repeat('9', 400) . '.5', \PHP_FLOAT_MAX, \str_repeat('9', 400) . '.5', '0'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $mantissa
     * @param non-empty-string $exponent
     */
    #[DataProvider('overflowDataProvider')]
    public function testFloatOutOfTheRangeIsTheNearestBound(
        string $source,
        float $value,
        string $mantissa,
        string $exponent,
    ): void {
        $node = $this->parse($source);

        self::assertInstanceOf(FloatLiteralNode::class, $node);
        self::assertSame($value, $node->value);
        self::assertTrue(\is_finite($node->value));
        self::assertSame($mantissa, $node->mantissa);
        self::assertSame($exponent, $node->exponent);
        self::assertSame($source, $node->raw);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string}>
     */
    public static function underflowDataProvider(): iterable
    {
        // source, mantissa, exponent
        yield 'positive' => ['1e-400', '1', '-400'];
        yield 'negative' => ['-1e-400', '-1', '-400'];
        yield 'with a fraction' => ['1.5e-400', '1.5', '-400'];
        yield 'long fraction part' => ['0.' . \str_repeat('0', 400) . '1', '0.' . \str_repeat('0', 400) . '1', '0'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $mantissa
     * @param non-empty-string $exponent
     */
    #[DataProvider('underflowDataProvider')]
    public function testFloatCloseToZeroIsZeroAndKeepsTheExactNumber(
        string $source,
        string $mantissa,
        string $exponent,
    ): void {
        $node = $this->parse($source);

        self::assertInstanceOf(FloatLiteralNode::class, $node);
        self::assertSame(0.0, \abs($node->value));
        self::assertSame($mantissa, $node->mantissa);
        self::assertSame($exponent, $node->exponent);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, float}>
     */
    public static function boundDataProvider(): iterable
    {
        yield 'the largest float' => ['1.7976931348623157e308', \PHP_FLOAT_MAX];
        yield 'the negative largest float' => ['-1.7976931348623157e308', -\PHP_FLOAT_MAX];
        yield 'the smallest normal float' => ['2.2250738585072014e-308', \PHP_FLOAT_MIN];
    }

    #[DataProvider('boundDataProvider')]
    public function testFloatAtTheBoundIsNotChanged(string $source, float $value): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(FloatLiteralNode::class, $node);
        self::assertSame($value, $node->value);
    }
}
