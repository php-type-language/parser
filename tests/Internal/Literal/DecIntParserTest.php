<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;
use TypeLang\Parser\Internal\SubParser\DecIntParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for a number out of the native range: PHP reads it as a float, the
 * node holds the nearest bound as a value and the exact number as a decimal.
 */
final class DecIntParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, numeric-string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'simple' => ['42', 42, '42'];
        yield 'signed' => ['+42', 42, '42'];
        yield 'negative' => ['-42', -42, '-42'];
        yield 'with separators' => ['1_000_000', 1000000, '1000000'];
        yield 'int max' => ['9223372036854775807', \PHP_INT_MAX, '9223372036854775807'];
        yield 'int min' => ['-9223372036854775808', \PHP_INT_MIN, '-9223372036854775808'];
        yield 'int min plus one' => ['-9223372036854775807', -\PHP_INT_MAX, '-9223372036854775807'];
        yield 'above int max' => ['9223372036854775808', \PHP_INT_MAX, '9223372036854775808'];
        yield 'far above int max' => ['18446744073709551616', \PHP_INT_MAX, '18446744073709551616'];
        yield 'below int min' => ['-9223372036854775809', \PHP_INT_MIN, '-9223372036854775809'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $decimal
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueAndDecimalAreDecoded(string $raw, int $value, string $decimal): void
    {
        $node = (new DecIntParser(new NativeBaseConverter()))->parse($raw);

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
        $node = (new DecIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame(IntLiteralKind::Decimal, $node->kind);
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
        $raw = (new IntLiteralNode($value, kind: IntLiteralKind::Decimal))->raw;
        $node = (new DecIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame((string) $value, $node->decimal);
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new DecIntParser(new NativeBaseConverter()))->parse('-1_000', 42);

        self::assertSame('-1_000', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
