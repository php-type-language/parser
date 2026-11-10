<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;
use TypeLang\Parser\Internal\SubParser\BinIntParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for a number out of the native range: PHP reads it as a float, the
 * node holds the nearest bound as a value and the exact number as a decimal.
 */
final class BinIntParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, numeric-string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'simple' => ['0b101', 5, '5'];
        yield 'upper case prefix' => ['0B101', 5, '5'];
        yield 'with separators' => ['0b1_01', 5, '5'];
        yield 'signed' => ['+0b11', 3, '3'];
        yield 'negative' => ['-0b11', -3, '-3'];
        yield 'zero' => ['0b0', 0, '0'];
        yield 'negative zero' => ['-0b0', 0, '0'];
        yield 'int max' => ['0b' . \str_repeat('1', 63), \PHP_INT_MAX, '9223372036854775807'];
        yield 'int min' => ['-0b1' . \str_repeat('0', 63), \PHP_INT_MIN, '-9223372036854775808'];
        yield 'int min plus one' => ['-0b' . \str_repeat('1', 63), -\PHP_INT_MAX, '-9223372036854775807'];
        yield 'above int max' => ['0b1' . \str_repeat('0', 63), \PHP_INT_MAX, '9223372036854775808'];
        yield 'twice int max' => ['0b' . \str_repeat('1', 64), \PHP_INT_MAX, '18446744073709551615'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $decimal
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueAndDecimalAreDecoded(string $raw, int $value, string $decimal): void
    {
        $node = (new BinIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame($decimal, $node->decimal);
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testKindIsTheNotationOfTheParser(string $raw): void
    {
        $node = (new BinIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame(IntLiteralKind::Binary, $node->kind);
    }

    /**
     * @return iterable<non-empty-string, array{int}>
     */
    public static function derivedRawDataProvider(): iterable
    {
        yield 'zero' => [0];
        yield 'positive' => [255];
        yield 'negative' => [-255];
        yield 'upper bound' => [\PHP_INT_MAX];
        yield 'lower bound' => [\PHP_INT_MIN];
    }

    #[Test]
    #[DataProvider('derivedRawDataProvider')]
    public function testRawDerivedByTheNodeIsDecodedBack(int $value): void
    {
        $raw = (new IntLiteralNode($value, kind: IntLiteralKind::Binary))->raw;
        $node = (new BinIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame((string) $value, $node->decimal);
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new BinIntParser(new NativeBaseConverter()))->parse('-0B10_10', 42);

        self::assertSame('-0B10_10', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
