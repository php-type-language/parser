<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;
use TypeLang\Parser\Internal\SubParser\OctLegacyIntParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralKind;

/**
 * The expected values are the ones PHP itself gives for the same source,
 * except for a number out of the native range: PHP reads it as a float, the
 * node holds the nearest bound as a value and the exact number as a decimal.
 */
final class OctLegacyIntParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, numeric-string}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'simple' => ['017', 15, '15'];
        yield 'with a separator' => ['0_42', 34, '34'];
        yield 'zeros' => ['00', 0, '0'];
        yield 'lone zero' => ['0', 0, '0'];
        yield 'signed zero' => ['+0', 0, '0'];
        yield 'negative zero' => ['-0', 0, '0'];
        yield 'signed' => ['+017', 15, '15'];
        yield 'negative' => ['-017', -15, '-15'];
        yield 'int max' => ['0777777777777777777777', \PHP_INT_MAX, '9223372036854775807'];
        yield 'int min' => ['-01000000000000000000000', \PHP_INT_MIN, '-9223372036854775808'];
        yield 'int min plus one' => ['-0777777777777777777777', -\PHP_INT_MAX, '-9223372036854775807'];
        yield 'above int max' => ['01000000000000000000000', \PHP_INT_MAX, '9223372036854775808'];
        yield 'twice int max' => ['01777777777777777777777', \PHP_INT_MAX, '18446744073709551615'];
    }

    /**
     * @param non-empty-string $raw
     * @param numeric-string $decimal
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testValueAndDecimalAreDecoded(string $raw, int $value, string $decimal): void
    {
        $node = (new OctLegacyIntParser(new NativeBaseConverter()))->parse($raw);

        self::assertSame($value, $node->value);
        self::assertSame($decimal, $node->decimal);
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('valueDataProvider')]
    public function testKindIsTheNotationOfTheParser(string $raw, int $value, string $_decimal): void
    {
        $node = (new OctLegacyIntParser(new NativeBaseConverter()))->parse($raw);

        // A lone zero is a decimal number, any other legacy octal stays octal
        self::assertSame($value === 0 ? IntLiteralKind::Decimal : IntLiteralKind::Octal, $node->kind);
    }

    #[Test]
    public function testRawAndOffsetAreKept(): void
    {
        $node = (new OctLegacyIntParser(new NativeBaseConverter()))->parse('-0_42', 42);

        self::assertSame('-0_42', $node->raw);
        self::assertSame(42, $node->offset);
    }
}
