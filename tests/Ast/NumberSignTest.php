<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;

/**
 * A sign of a number is a part of the literal, the gap behind it is allowed
 * as it is in PHP: `- 42.0` is `-42.0`.
 */
final class NumberSignTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function intDataProvider(): iterable
    {
        yield 'negative' => ['-42', -42];
        yield 'negative with a space' => ['- 42', -42];
        yield 'negative with spaces' => ['-   42', -42];
        yield 'negative with a tab' => ["-\t42", -42];
        yield 'negative with a line break' => ["-\n42", -42];
        yield 'negative with a line break and spaces' => ["- \r\n 42", -42];
        yield 'positive' => ['+42', 42];
        yield 'positive with a space' => ['+ 42', 42];
        yield 'positive with spaces' => ['+   42', 42];
        yield 'negative with a separator' => ['- 1_000', -1000];
        yield 'negative hexadecimal with a space' => ['- 0x1F', -31];
        yield 'positive hexadecimal with a space' => ['+ 0x1F', 31];
        yield 'negative binary with a space' => ['- 0b101', -5];
        yield 'negative octal with a space' => ['- 0o17', -15];
        yield 'negative legacy octal with a space' => ['- 017', -15];
    }

    #[DataProvider('intDataProvider')]
    public function testIntegerWithASign(string $source, int $expected): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(IntLiteralNode::class, $node);
        self::assertSame($expected, $node->value);
        self::assertSame($source, $node->raw);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, float}>
     */
    public static function floatDataProvider(): iterable
    {
        yield 'negative' => ['-1.5', -1.5];
        yield 'negative with a space' => ['- 1.5', -1.5];
        yield 'negative with spaces' => ['-   1.5', -1.5];
        yield 'negative with a line break' => ["-\n1.5", -1.5];
        yield 'positive' => ['+1.5', 1.5];
        yield 'positive with a space' => ['+ 1.5', 1.5];
        yield 'negative without an integer part' => ['- .5', -0.5];
        yield 'positive without an integer part' => ['+ .5', 0.5];
        yield 'negative without a fraction part' => ['- 1.', -1.0];
        yield 'negative with an exponent' => ['- 1e3', -1000.0];
        yield 'positive with an exponent' => ['+ 1e3', 1000.0];
        yield 'negative with a signed exponent' => ['- 1.5e-2', -0.015];
        yield 'negative with separators' => ['- 1_0.2_5', -10.25];
    }

    #[DataProvider('floatDataProvider')]
    public function testFloatWithASign(string $source, float $expected): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(FloatLiteralNode::class, $node);
        self::assertSame($expected, $node->value);
        self::assertSame($source, $node->raw);
    }

    public function testMantissaAndExponentOfAFloatWithASpaceAreCleaned(): void
    {
        $node = $this->parse('- 1.5e-2');

        self::assertInstanceOf(FloatLiteralNode::class, $node);
        self::assertSame('-1.5', $node->mantissa);
        self::assertSame('-2', $node->exponent);
    }

    public function testSignedNumbersInAUnionAreSeparateMembers(): void
    {
        $node = $this->parse('- 1|+ 2.5');

        self::assertInstanceOf(UnionTypeNode::class, $node);
        self::assertInstanceOf(IntLiteralNode::class, $node->types[0]);
        self::assertSame(-1, $node->types[0]->value);
        self::assertInstanceOf(FloatLiteralNode::class, $node->types[1]);
        self::assertSame(2.5, $node->types[1]->value);
    }
}
