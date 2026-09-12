<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for conditional (ternary) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ConditionalTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function conditionDataProvider(): iterable
    {
        yield 'is' => ['A is B ? C : D', 'EqualConditionNode'];
        yield 'is not' => ['A is not B ? C : D', 'NotEqualConditionNode'];
    }

    #[DataProvider('conditionDataProvider')]
    public function testConditionalOperators(string $type, string $condition): void
    {
        self::assertSame(<<<AST
            TernaryExpressionNode
              Condition\\{$condition}
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              NamedTypeNode
                Name(D)
            AST, $this->parseAndPrint($type));
    }

    public function testConditionalWithVariableSubject(): void
    {
        self::assertSame(<<<'AST'
            TernaryExpressionNode
              Condition\EqualConditionNode
                VariableNode
                  Identifier(T)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              NamedTypeNode
                Name(D)
            AST, $this->parseAndPrint('$T is B ? C : D'));
    }

    /**
     * A variable stands on either side of the operator, and on both at once.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableOperandDataProvider(): iterable
    {
        yield 'on the left' => ['($T is B ? C : D)'];
        yield 'on the right' => ['(A is $T ? C : D)'];
        yield 'on both sides' => ['($A is $B ? C : D)'];
        yield 'this on the left' => ['($this is B ? C : D)'];
        yield 'this on the right' => ['(A is $this ? C : D)'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('variableOperandDataProvider')]
    public function testVariableStandsAsAnOperand(string $type): void
    {
        $printer = new \TypeLang\Printer\PrettyTypePrinter();

        self::assertSame($type, $printer->print($this->parse($type)));
    }

    public function testEqualityOperatorIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "="');

        $this->parse('A == B ? C : D');
    }

    public function testInequalityOperatorIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "!="');

        $this->parse('A != B ? C : D');
    }

    public function testBareQuestionMarkIsNotAConditional(): void
    {
        $this->expectParsingException('unexpected "?"');

        $this->parse('T ? U : V');
    }
}
