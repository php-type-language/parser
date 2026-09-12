<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * Tests for the integer literals the parser builds out of their raw
 * representation.
 */
final class IntLiteralTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    private function literal(string $code): IntLiteralNode
    {
        $node = $this->parse($code);

        self::assertInstanceOf(IntLiteralNode::class, $node);

        return $node;
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int, non-empty-string}>
     */
    public static function provideIntegers(): iterable
    {
        yield 'zero' => ['0', 0, '0'];
        yield 'decimal' => ['42', 42, '42'];
        yield 'negative decimal' => ['-42', -42, '-42'];
        yield 'signed decimal' => ['+42', 42, '42'];
        yield 'signed hexadecimal' => ['+0x1F', 31, '31'];
        yield 'hexadecimal' => ['0x1F', 31, '31'];
        yield 'uppercase hexadecimal prefix' => ['0X1F', 31, '31'];
        yield 'negative hexadecimal' => ['-0x10', -16, '-16'];
        yield 'binary' => ['0b1010', 10, '10'];
        yield 'uppercase binary prefix' => ['0B1010', 10, '10'];
        yield 'octal' => ['0o17', 15, '15'];
        yield 'uppercase octal prefix' => ['0O17', 15, '15'];
        yield 'legacy octal' => ['017', 15, '15'];
        yield 'underscored' => ['1_000_000', 1000000, '1000000'];
        yield 'underscored hexadecimal' => ['0xFF_FF', 65535, '65535'];
    }

    /**
     * @param non-empty-string $literal
     * @param non-empty-string $decimal
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('provideIntegers')]
    public function integerIsParsedToItsDecimalValue(string $literal, int $value, string $decimal): void
    {
        $node = $this->literal($literal);

        self::assertSame($value, $node->value);
        self::assertSame($decimal, $node->decimal);
    }

    /**
     * @param non-empty-string $literal
     * @param non-empty-string $decimal
     * @throws \Throwable
     */
    #[Test]
    #[DataProvider('provideIntegers')]
    public function integerParsingKeepsTheOriginalRepresentation(string $literal, int $value, string $decimal): void
    {
        self::assertSame($literal, $this->literal($literal)->raw);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function integerParsingSupportsPhpIntMin(): void
    {
        self::assertSame(\PHP_INT_MIN, $this->literal((string) \PHP_INT_MIN)->value);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function integerParsingSupportsPhpIntMax(): void
    {
        self::assertSame(\PHP_INT_MAX, $this->literal((string) \PHP_INT_MAX)->value);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function negativeZeroIsParsedAsZero(): void
    {
        self::assertSame(0, $this->literal('-0')->value);
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function theOffsetOfALiteralIsTheOneItIsWrittenAt(): void
    {
        $node = $this->parse('array{a: 42}');

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->fields);

        $field = $node->fields->items[0];

        self::assertInstanceOf(\TypeLang\Type\Shape\NamedFieldNode::class, $field);
        self::assertInstanceOf(IntLiteralNode::class, $field->type);
        self::assertSame(9, $field->type->offset);
    }
}
