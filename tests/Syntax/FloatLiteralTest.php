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
        yield 'signed' => ['+0.9', 0.9];
        yield 'signed non-prefixed' => ['+.9', 0.9];

        yield 'scientific' => ['10e2', 1000.0];
        yield 'scientific uppercase' => ['10E2', 1000.0];
        yield 'scientific negative exponent' => ['10e-2', 0.1];
        yield 'scientific signed exponent' => ['10e+2', 1000.0];
        yield 'signed scientific' => ['+10e2', 1000.0];
        yield 'signed scientific with signed exponent' => ['+1.5e+3', 1500.0];

        yield 'separated integer part' => ['4_4.', 44.0];
        yield 'separated fraction part' => ['.4_2', 0.42];
        yield 'separated exponent' => ['42e2_3', 42e23];
        yield 'separated signed exponent' => ['42e+2_3', 42e23];
        yield 'separated in every part' => ['2_3.4_5e-6_7', 23.45e-67];
    }

    /**
     * A separator is only allowed between two digits.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function invalidSeparatorDataProvider(): iterable
    {
        yield 'before the dot' => ['1_.5', 'unexpected "_"'];
        yield 'after the dot' => ['1._5', 'unexpected "_5"'];
        yield 'before the exponent' => ['1.5_e3', 'unexpected "_e3"'];
        yield 'inside the exponent' => ['1.5e_3', 'unexpected "e_3"'];
        yield 'trailing' => ['1e3_', 'unexpected "_"'];
    }

    #[DataProvider('invalidSeparatorDataProvider')]
    public function testSeparatorBelongsBetweenDigits(string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type);
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
