<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Condition\ConditionNode\ConditionKind;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\VariableNode;

/**
 * Tests for the conditional (ternary) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ConditionalTest extends AstTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, ConditionKind}>
     */
    public static function conditionDataProvider(): iterable
    {
        yield 'is' => ['A is B ? C : D', ConditionKind::Equal];
        yield 'is not' => ['A is not B ? C : D', ConditionKind::NotEqual];
    }

    #[DataProvider('conditionDataProvider')]
    public function testConditionalOperators(string $type, ConditionKind $kind): void
    {
        $node = $this->parse($type);

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertSame($kind, $node->condition->kind);
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
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
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                VariableNode($T)
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

    /**
     * A condition reads its subject as a type first, so the word is the type
     * and not the variable it is spelled like.
     */
    public function testThisStandsAsATypeOnTheLeftOfACondition(): void
    {
        $node = $this->parse('$this is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(ThisTypeNode::class, $node->condition->subject);
    }

    /**
     * The token is written the one way it is written, so a word that only
     * looks like it is an ordinary variable.
     */
    public function testTheThisTokenIsCaseSensitive(): void
    {
        $node = $this->parse('$This is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame('This', $node->condition->subject->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableNameDataProvider(): iterable
    {
        yield 'a word' => ['$value'];
        yield 'an underscore' => ['$value_1'];
        yield 'a leading underscore' => ['$_value'];
        yield 'digits' => ['$value42'];
        yield 'a word that opens with this' => ['$thisValue'];
    }

    /**
     * @param non-empty-string $variable
     * @throws \Throwable
     */
    #[DataProvider('variableNameDataProvider')]
    public function testVariableIsTheSubjectOfACondition(string $variable): void
    {
        $node = $this->parse($variable . ' is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame(\substr($variable, 1), $node->condition->subject->name);
    }

    /**
     * The "is not" is one operator, so the word behind the "is" is never the
     * target.
     */
    public function testIsNotIsOneOperator(): void
    {
        $node = $this->parse('T is not ?A ? X : Y');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertSame(ConditionKind::NotEqual, $node->condition->kind);
    }

    /**
     * A word that only opens with "not" is a name, it is no operator.
     */
    public function testNameThatOpensWithNotIsATarget(): void
    {
        $node = $this->parse('T is notX ? A : B');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertSame(ConditionKind::Equal, $node->condition->kind);
        self::assertInstanceOf(NamedTypeNode::class, $node->condition->target);
        self::assertSame('notX', $node->condition->target->name->toString());
    }

    public function testNegationHoldsForANullableTarget(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                NamedTypeNode
                  Name(T)
                WrappingTypeNode\NullableTypeNode
                  NamedTypeNode
                    Name(A)
              NamedTypeNode
                Name(X)
              NamedTypeNode
                Name(Y)
            AST, $this->parseAndPrint('T is not ?A ? X : Y'));
    }

    /**
     * The first question mark asks the condition, the second one makes the
     * branch nullable.
     */
    public function testTwoQuestionMarksAreAConditionAndANullableBranch(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                NamedTypeNode
                  Name(T)
                NamedTypeNode
                  Name(int)
              WrappingTypeNode\NullableTypeNode
                NamedTypeNode
                  Name(A)
              NamedTypeNode
                Name(B)
            AST, $this->parseAndPrint('T is int ?? A : B'));
    }

    public function testConditionNestsToTheRightInTheElseBranch(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              Condition\ConditionalTypeNode
                Condition\ConditionNode
                  NamedTypeNode
                    Name(D)
                  NamedTypeNode
                    Name(E)
                NamedTypeNode
                  Name(F)
                NamedTypeNode
                  Name(G)
            AST, $this->parseAndPrint('A is B ? C : D is E ? F : G'));
    }

    public function testConditionNestsInTheThenBranch(): void
    {
        $node = $this->parse('T is int ? A is C ? D : E : B');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(ConditionalTypeNode::class, $node->then);
        self::assertNotInstanceOf(ConditionalTypeNode::class, $node->else);
    }

    /**
     * The subject is the whole union, a condition is weaker than the bar.
     */
    public function testConditionIsWeakerThanAUnion(): void
    {
        $node = $this->parse('C|T is int ? A : B');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertSame(
            'LogicalTypeNode\UnionTypeNode',
            \explode("\n", $this->print($node->condition->subject))[0],
        );
    }

    /**
     * The `...$a` is the name of a variadic parameter, unless a condition
     * with a variable for its subject stands behind it.
     */
    public function testConditionWithAVariableSubjectIsTheTypeOfAVariadicParameter(): void
    {
        $node = $this->parse('callable(...$a is int ? A : B)');

        self::assertInstanceOf(CallableTypeNode::class, $node);

        $parameter = $node->parameters[0];

        self::assertInstanceOf(CallableParameterNode::class, $parameter);
        self::assertTrue($parameter->isVariadic);
        self::assertNull($parameter->name);
        self::assertInstanceOf(ConditionalTypeNode::class, $parameter->type);
        self::assertInstanceOf(VariableNode::class, $parameter->type->condition->subject);
    }

    public function testVariadicParameterNamedLikeASubjectIsStillAName(): void
    {
        $node = $this->parse('callable(...$a, $b)');

        self::assertInstanceOf(CallableTypeNode::class, $node);

        $parameter = $node->parameters[0];

        self::assertInstanceOf(CallableParameterNode::class, $parameter);
        self::assertTrue($parameter->isVariadic);
        self::assertNull($parameter->type);
        self::assertInstanceOf(VariableNode::class, $parameter->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>, int<0, max>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        // The source, the offsets of the condition, the target, the then and the else branches
        yield 'no trivia' => ['T is int ? A : B', 0, 5, 11, 15];
        yield 'spaces before' => ['  T is int ? A : B', 2, 7, 13, 17];
        yield 'negation' => ['T is not int ? A : B', 0, 9, 15, 19];
        yield 'variable subject' => ['$a is int ? A : B', 0, 6, 12, 16];
        yield 'comments' => ['T /* a */ is /* b */ int ? /* c */ A : /* d */ B', 0, 21, 35, 47];
    }

    /**
     * The condition begins where the whole type does, at its subject.
     *
     * @param non-empty-string $source
     * @param int<0, max> $target
     * @param int<0, max> $then
     * @param int<0, max> $else
     * @param int<0, max> $subject
     * @throws \Throwable
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsets(string $source, int $subject, int $target, int $then, int $else): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertSame($subject, $node->offset);
        self::assertSame($subject, $node->condition->offset);
        self::assertSame($target, $node->condition->target->offset);
        self::assertSame($then, $node->then->offset);
        self::assertSame($else, $node->else->offset);
    }
}
