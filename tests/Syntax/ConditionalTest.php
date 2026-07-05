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
        yield 'less than' => ['A < B ? C : D', 'LessThanConditionNode'];
        yield 'greater than' => ['A > B ? C : D', 'GreaterThanConditionNode'];
        yield 'less or equal' => ['A <= B ? C : D', 'LessThanOrEqualConditionNode'];
        yield 'greater or equal' => ['A >= B ? C : D', 'GreaterThanOrEqualConditionNode'];
    }

    #[DataProvider('conditionDataProvider')]
    public function testConditionalOperators(string $type, string $condition): void
    {
        self::assertSame(<<<AST
            TernaryExpressionNode
              Condition\\{$condition}
                NamedTypeNode
                  Name(A)
                    Identifier(A)
                NamedTypeNode
                  Name(B)
                    Identifier(B)
              NamedTypeNode
                Name(C)
                  Identifier(C)
              NamedTypeNode
                Name(D)
                  Identifier(D)
            AST, $this->parseAndPrint($type));
    }

    public function testConditionalWithVariableSubject(): void
    {
        self::assertSame(<<<'AST'
            TernaryExpressionNode
              Condition\EqualConditionNode
                Literal\VariableLiteralNode($T)
                NamedTypeNode
                  Name(B)
                    Identifier(B)
              NamedTypeNode
                Name(C)
                  Identifier(C)
              NamedTypeNode
                Name(D)
                  Identifier(D)
            AST, $this->parseAndPrint('$T is B ? C : D'));
    }

    public function testEqualityOperatorIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "="');

        $this->parse('A == B ? C : D');
    }

    public function testInequalityOperatorIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "!"');

        $this->parse('A != B ? C : D');
    }

    public function testBareQuestionMarkIsNotAConditional(): void
    {
        $this->expectParsingException('unexpected "?"');

        $this->parse('T ? U : V');
    }
}
