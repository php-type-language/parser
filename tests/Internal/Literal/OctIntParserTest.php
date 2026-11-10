<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;
use TypeLang\Parser\Internal\SubParser\OctIntParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for a number out of the native range: PHP reads it as a float, the
 * node holds the nearest bound as a value and the exact number as a decimal.
 */
final class OctIntParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, numeric-string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'simple' => ['0o17', 15, '15'];
        yield 'upper case prefix' => ['0O17', 15, '15'];
        yield 'with separators' => ['0o6_5_5', 429, '429'];
        yield 'signed' => ['+0o17', 15, '15'];
        yield 'negative' => ['-0o17', -15, '-15'];
        yield 'zero' => ['0o0', 0, '0'];
        yield 'negative zero' => ['-0o0', 0, '0'];
        yield 'int max' => ['0o777777777777777777777', \PHP_INT_MAX, '9223372036854775807'];
        yield 'int min' => ['-0o1000000000000000000000', \PHP_INT_MIN, '-9223372036854775808'];
        yield 'int min plus one' => ['-0o777777777777777777777', -\PHP_INT_MAX, '-9223372036854775807'];
        yield 'above int max' => ['0o1000000000000000000000', \PHP_INT_MAX, '9223372036854775808'];
        yield 'twice int max' => ['0o1777777777777777777777', \PHP_INT_MAX, '18446744073709551615'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $decimal
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueAndDecimalAreDecoded(string $raw, int $value, string $decimal): void
    {
        $node = (new OctIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame($decimal, $node->decimal);
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testKindIsTheNotationOfTheParser(string $raw, int $_value, string $_decimal): void
    {
        $node = (new OctIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame(IntLiteralKind::Octal, $node->kind);
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
        $raw = (new IntLiteralNode($value, kind: IntLiteralKind::Octal))->raw;
        $node = (new OctIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame((string) $value, $node->decimal);
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new OctIntParser(new NativeBaseConverter()))->parse('-0o6_5_5', 42);

        self::assertSame('-0o6_5_5', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
