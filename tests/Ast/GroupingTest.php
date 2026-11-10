<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * Tests for the way the parts of a type bind to one another, that is, what
 * the parentheses group and what a suffix reaches over.
 */
#[Group('unit'), Group('type-lang/parser')]
final class GroupingTest extends AstTestCase
{
    /**
     * An intersection binds tighter than a union, so a union is the outer one
     * of the two.
     */
    public function testAnIntersectionBindsTighterThanAUnion(): void
    {
        self::assertSame(<<<'AST'
            LogicalTypeNode\UnionTypeNode
              LogicalTypeNode\IntersectionTypeNode
                NamedTypeNode
                  Name(int)
                NamedTypeNode
                  Name(string)
              NamedTypeNode
                Name(float)
            AST, $this->parseAndPrint('int&string|float'));
    }

    public function testParenthesesTurnAUnionIntoAMemberOfAnIntersection(): void
    {
        self::assertSame(<<<'AST'
            LogicalTypeNode\IntersectionTypeNode
              LogicalTypeNode\UnionTypeNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
            AST, $this->parseAndPrint('(A|B)&C'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function parenthesizedNameDataProvider(): iterable
    {
        yield 'a single pair' => ['(A)'];
        yield 'a pair of pairs' => ['((A))'];
        yield 'spaces inside' => ['(  A  )'];
    }

    /**
     * Parentheses group and nothing else, so a pair that groups a single type
     * reaches no tree.
     *
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('parenthesizedNameDataProvider')]
    public function testParenthesesAroundASingleTypeAreOfNoMeaning(string $type): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(A)
            AST, $this->parseAndPrint($type));
    }

    public function testTheQuestionMarkReachesOverAWholeGroup(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\NullableTypeNode
              LogicalTypeNode\UnionTypeNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
            AST, $this->parseAndPrint('?(A|B)'));
    }

    /**
     * A "[]" is read before the "?" in front of it, so the list is the one
     * made nullable and not its element.
     */
    public function testTheQuestionMarkReachesOverAListAndNotOverItsElement(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\NullableTypeNode
              WrappingTypeNode\TypesListTypeNode
                NamedTypeNode
                  Name(int)
            AST, $this->parseAndPrint('?int[]'));
    }

    public function testAListIsMadeOfAWholeGroup(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypesListTypeNode
              LogicalTypeNode\UnionTypeNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
            AST, $this->parseAndPrint('(A|B)[]'));
    }

    public function testListsNestFromTheInsideOut(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypesListTypeNode
              WrappingTypeNode\TypesListTypeNode
                NamedTypeNode
                  Name(int)
            AST, $this->parseAndPrint('int[][]'));
    }

    public function testAListFollowsAnOffsetItIsWrittenBehind(): void
    {
        self::assertSame(<<<'AST'
            WrappingTypeNode\TypesListTypeNode
              TypeOffsetAccessTypeNode
                NamedTypeNode
                  Name(T)
                NamedTypeNode
                  Name(K)
            AST, $this->parseAndPrint('T[K][]'));

        $node = $this->parse('T[K][]');

        self::assertInstanceOf(TypesListTypeNode::class, $node);
        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node->type);
        self::assertInstanceOf(NamedTypeNode::class, $node->type->access);
        self::assertSame('K', $node->type->access->name->toString());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function listedPrimaryTypeDataProvider(): iterable
    {
        yield 'a generic type' => ['list<int>[]'];
        yield 'a shape' => ['array{a: int}[]'];
        yield 'a class constant' => ['T::CONST[]'];
        yield 'a constant mask' => ['JSON_*[]'];
        yield 'a literal' => ["'a'[]"];
        yield 'a group' => ['(int)[]'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('listedPrimaryTypeDataProvider')]
    public function testEveryPrimaryTypeCarriesAList(string $type): void
    {
        self::assertStringStartsWith('WrappingTypeNode\TypesListTypeNode', $this->parseAndPrint($type));
    }

    public function testCallablesNestInTheirParameters(): void
    {
        self::assertSame(<<<'AST'
            Callable\CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isByReference=false, isVariadic=false)
                  Callable\CallableTypeNode
                    Name(callable)
                    Callable\CallableParameterListNode
                      Callable\CallableParameterNode(isByReference=false, isVariadic=false)
                        NamedTypeNode
                          Name(int)
                    NamedTypeNode
                      Name(string)
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable(callable(int): string): void'));
    }

    public function testACallableReturnsACallable(): void
    {
        self::assertSame(<<<'AST'
            Callable\CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
              Callable\CallableTypeNode
                Name(callable)
                Callable\CallableParameterListNode
                NamedTypeNode
                  Name(void)
            AST, $this->parseAndPrint('callable(): callable(): void'));
    }

    /**
     * The branch a condition chooses is a type whole, so a condition of its
     * own stands there as well.
     */
    public function testAConditionNestsInTheBranchOfAnother(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              Condition\ConditionalTypeNode
                Condition\ConditionNode
                  NamedTypeNode
                    Name(C)
                  NamedTypeNode
                    Name(D)
                NamedTypeNode
                  Name(E)
                NamedTypeNode
                  Name(F)
              NamedTypeNode
                Name(G)
            AST, $this->parseAndPrint('A is B ? C is D ? E : F : G'));
    }

    public function testAUnionReachesIntoTheBranchOfACondition(): void
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
              LogicalTypeNode\UnionTypeNode
                NamedTypeNode
                  Name(D)
                NamedTypeNode
                  Name(E)
            AST, $this->parseAndPrint('A is B ? C : D|E'));
    }

    public function testTheSubjectOfAConditionIsAWholeUnion(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\ConditionNode
                LogicalTypeNode\UnionTypeNode
                  NamedTypeNode
                    Name(int)
                  NamedTypeNode
                    Name(string)
                NamedTypeNode
                  Name(A)
              NamedTypeNode
                Name(B)
              NamedTypeNode
                Name(C)
            AST, $this->parseAndPrint('int|string is A ? B : C'));
    }

    public function testShapesNestInOneAnother(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(array)
                    Shape\FieldListNode(isSealed=true)
                      Shape\NamedFieldNode(isOptional=false)
                        Identifier(b)
                        NamedTypeNode
                          Name(int)
            AST, $this->parseAndPrint('array{a: array{b: int}}'));
    }

    public function testAGroupStandsAsAShapeValue(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  LogicalTypeNode\UnionTypeNode
                    NamedTypeNode
                      Name(A)
                    NamedTypeNode
                      Name(B)
            AST, $this->parseAndPrint('array{a: (A|B)}'));
    }

    public function testAnEmptyGroupIsNoType(): void
    {
        $this->expectParsingException('A type is expected after "("');

        $this->parse('()');
    }

    public function testAGroupIsClosedByTheParenthesisItIsOpenedWith(): void
    {
        $this->expectParsingException();

        $this->parse('(int');
    }

    public function testAnAngleBracketOfItsOwnFollowsNoArgumentList(): void
    {
        $this->expectParsingException('Unexpected ">"');

        $this->parse('Some<int>>');
    }
}
