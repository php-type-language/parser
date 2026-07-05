<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Literal\FloatLiteralNode;

/**
 * Tests for the float literal grammar (basic and scientific notation).
 */
#[Group('unit'), Group('type-lang/parser')]
final class FloatLiteralTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, float}>
     */
    public static function validFloatDataProvider(): iterable
    {
        yield 'simple' => ['0.9', 0.9];
        yield 'non-prefixed' => ['.9', 0.9];
        yield 'non-suffixed' => ['1.', 1.0];
        yield 'negative' => ['-0.9', -0.9];

        yield 'scientific' => ['10e2', 1000.0];
        yield 'scientific uppercase' => ['10E2', 1000.0];
        yield 'scientific negative exponent' => ['10e-2', 0.1];
    }

    #[DataProvider('validFloatDataProvider')]
    public function testValidFloats(string $type, float $expected): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(FloatLiteralNode::class, $statement);
        self::assertSame($expected, $statement->value);
    }

    public function testLeadingAndTrailingNumberCannotBothBeOmitted(): void
    {
        $this->expectParsingException('unexpected "."');

        $this->parse('.');
    }

    public function testFloatAllowsOnlyDigits(): void
    {
        $this->expectParsingException('unexpected "A"');

        $this->parse('0.0A');
    }

    public function testScientificExponentMustBeDecimal(): void
    {
        $this->expectParsingException('unexpected "e-F"');

        $this->parse('10e-F');
    }
}
