<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;
use TypeLang\Parser\Internal\SubParser\HexIntParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for a number out of the native range: PHP reads it as a float, the
 * node holds the nearest bound as a value and the exact number as a decimal.
 */
final class HexIntParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, numeric-string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'simple' => ['0x1F', 31, '31'];
        yield 'lower case' => ['0xdead', 57005, '57005'];
        yield 'upper case prefix' => ['0XDEad', 57005, '57005'];
        yield 'with separators' => ['0xDE_AD', 57005, '57005'];
        yield 'signed' => ['+0xFF', 255, '255'];
        yield 'negative' => ['-0x10', -16, '-16'];
        yield 'zero' => ['0x0', 0, '0'];
        yield 'negative zero' => ['-0x0', 0, '0'];
        yield 'zeros' => ['0x00_00', 0, '0'];
        yield 'int max' => ['0x7FFFFFFFFFFFFFFF', \PHP_INT_MAX, '9223372036854775807'];
        yield 'int min' => ['-0x8000000000000000', \PHP_INT_MIN, '-9223372036854775808'];
        yield 'int min plus one' => ['-0x7FFFFFFFFFFFFFFF', -\PHP_INT_MAX, '-9223372036854775807'];
        yield 'above int max' => ['0x8000000000000000', \PHP_INT_MAX, '9223372036854775808'];
        yield 'twice int max' => ['0xFFFFFFFFFFFFFFFF', \PHP_INT_MAX, '18446744073709551615'];
        yield 'below int min' => ['-0xFFFFFFFFFFFFFFFF', \PHP_INT_MIN, '-18446744073709551615'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $decimal
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueAndDecimalAreDecoded(string $raw, int $value, string $decimal): void
    {
        $node = (new HexIntParser(new NativeBaseConverter()))->parse($raw);

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
        $node = (new HexIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame(IntLiteralKind::Hexadecimal, $node->kind);
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
        $raw = (new IntLiteralNode($value, kind: IntLiteralKind::Hexadecimal))->raw;
        $node = (new HexIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame((string) $value, $node->decimal);
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new HexIntParser(new NativeBaseConverter()))->parse('-0xDE_AD', 42);

        self::assertSame('-0xDE_AD', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
