<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The ampersand joins the types around it into one intersection.
 */
final class IntersectionTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, list<class-string<TypeNode>>}>
     */
    public static function membersDataProvider(): iterable
    {
        yield 'two names' => ['A&B', [NamedTypeNode::class, NamedTypeNode::class]];
        yield 'three names' => ['A&B&C', [NamedTypeNode::class, NamedTypeNode::class, NamedTypeNode::class]];
        yield 'literals' => ['1&2', [IntLiteralNode::class, IntLiteralNode::class]];
        yield 'this and a name' => ['$this&A', [ThisTypeNode::class, NamedTypeNode::class]];
        yield 'constants' => ['Foo::A&Foo::B', [ClassConstTypeNode::class, ClassConstTypeNode::class]];
        yield 'lists' => ['A[]&B[]', [TypesListTypeNode::class, TypesListTypeNode::class]];
        yield 'list and name' => ['A[]&B', [TypesListTypeNode::class, NamedTypeNode::class]];
        yield 'name and list' => ['A&B[]', [NamedTypeNode::class, TypesListTypeNode::class]];
        yield 'accesses' => ['A[K]&B[L]', [TypeOffsetAccessTypeNode::class, TypeOffsetAccessTypeNode::class]];
        yield 'nullable in a group on the left' => ['(?A)&B', [NullableTypeNode::class, NamedTypeNode::class]];
        yield 'nullable in a group on the right' => ['A&(?B)', [NamedTypeNode::class, NullableTypeNode::class]];
        yield 'nullables in groups' => ['(?A)&(?B)', [NullableTypeNode::class, NullableTypeNode::class]];
        yield 'nullable on the left' => ['?A&B', [NullableTypeNode::class, NamedTypeNode::class]];
        yield 'nullable on the right' => ['A&?B', [NamedTypeNode::class, NullableTypeNode::class]];
        yield 'nullable list' => ['?A[]&B', [NullableTypeNode::class, NamedTypeNode::class]];
        yield 'nullable list in a group' => ['(?A[])&B', [NullableTypeNode::class, NamedTypeNode::class]];
        yield 'nullable in a group in the middle' => [
            'A&(?B)&C',
            [NamedTypeNode::class, NullableTypeNode::class, NamedTypeNode::class],
        ];
        yield 'groups' => ['(A)&(B)', [NamedTypeNode::class, NamedTypeNode::class]];
        yield 'spaces' => ['A  &  B', [NamedTypeNode::class, NamedTypeNode::class]];
        yield 'line breaks' => ["A\n&\nB", [NamedTypeNode::class, NamedTypeNode::class]];
        yield 'comments' => ['A/* a */&/* b */B', [NamedTypeNode::class, NamedTypeNode::class]];
    }

    /**
     * @param non-empty-string $source
     * @param list<class-string<TypeNode>> $members
     */
    #[DataProvider('membersDataProvider')]
    public function testIntersectionKeepsItsMembersInTheOrderTheyAreWritten(string $source, array $members): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertCount(\count($members), $type->types);

        foreach ($members as $index => $class) {
            self::assertInstanceOf($class, $type->types[$index]);
        }
    }

    public function testMembersAreKeptAsTheyAreWritten(): void
    {
        $type = $this->parse('\\Some\\Any&Other&$this');

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->types[0]);
        self::assertSame('\\Some\\Any', $type->types[0]->name->toString());
        self::assertInstanceOf(NamedTypeNode::class, $type->types[1]);
        self::assertSame('Other', $type->types[1]->name->toString());
        self::assertInstanceOf(ThisTypeNode::class, $type->types[2]);
    }

    public function testManyTypesAreOneIntersectionNotANestedOne(): void
    {
        $type = $this->parse('A&B&C&D&E');

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertCount(5, $type->types);

        foreach ($type->types as $member) {
            self::assertInstanceOf(NamedTypeNode::class, $member);
        }
    }

    /**
     * Intersections are associative, a group of an intersection inside of another
     * intersection is merged with it.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<non-empty-string>}>
     */
    public static function mergedDataProvider(): iterable
    {
        yield 'group on the left' => ['(A&B)&C', ['A', 'B', 'C']];
        yield 'group on the right' => ['A&(B&C)', ['A', 'B', 'C']];
        yield 'groups on both sides' => ['(A&B)&(C&D)', ['A', 'B', 'C', 'D']];
        yield 'nested groups' => ['((A&B)&C)&D', ['A', 'B', 'C', 'D']];
        yield 'deep group' => ['A&(B&(C&(D&E)))', ['A', 'B', 'C', 'D', 'E']];
    }

    /**
     * @param non-empty-string $source
     * @param list<non-empty-string> $names
     */
    #[DataProvider('mergedDataProvider')]
    public function testGroupedIntersectionsAreMerged(string $source, array $names): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(IntersectionTypeNode::class, $type);

        $actual = [];

        foreach ($type->types as $member) {
            self::assertInstanceOf(NamedTypeNode::class, $member);
            $actual[] = $member->name->toString();
        }

        self::assertSame($names, $actual);
    }

    public function testGroupBehindASuffixIsNotMerged(): void
    {
        $type = $this->parse('(A&B)[]&C');

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertCount(2, $type->types);
        self::assertInstanceOf(TypesListTypeNode::class, $type->types[0]);
        self::assertInstanceOf(IntersectionTypeNode::class, $type->types[0]->type);
    }

    public function testGroupOfANullableIsNotMerged(): void
    {
        $type = $this->parse('(?(A&B))&C');

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertCount(2, $type->types);
        self::assertInstanceOf(NullableTypeNode::class, $type->types[0]);
        self::assertInstanceOf(IntersectionTypeNode::class, $type->types[0]->type);
    }

    /**
     * The question mark reaches over the member behind it only, a member is
     * nullable when the mark is the first thing of it.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<bool>}>
     */
    public static function memberMarkDataProvider(): iterable
    {
        yield 'first' => ['?A&B', [true, false]];
        yield 'first of three' => ['?A&B&C', [true, false, false]];
        yield 'second' => ['A&?B', [false, true]];
        yield 'both' => ['?A&?B', [true, true]];
        yield 'middle' => ['A&?B&C', [false, true, false]];
        yield 'last' => ['A&B&?C', [false, false, true]];
        yield 'all' => ['?A&?B&?C', [true, true, true]];
        yield 'all but the first' => ['A&?B&?C', [false, true, true]];
        yield 'nullable list' => ['A&?B[]', [false, true]];
        yield 'nullable access' => ['?A[K]&B', [true, false]];
        yield 'group of a nullable' => ['(?A)&B', [true, false]];
        yield 'group of a nullable on the right' => ['A&(?B)', [false, true]];
        yield 'nullable of a group of an intersection' => ['?(A&B)&C', [true, false]];
        yield 'space' => ['A & ? B', [false, true]];
        yield 'line breaks' => ["A\n&\n?\nB", [false, true]];
        yield 'comments' => ['A/* a */&/* b */?/* c */B', [false, true]];
    }

    /**
     * @param non-empty-string $source
     * @param list<bool> $nullable
     */
    #[DataProvider('memberMarkDataProvider')]
    public function testQuestionMarkIsTheOneOfItsMemberOnly(string $source, array $nullable): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(IntersectionTypeNode::class, $type);

        $actual = [];

        foreach ($type->types as $member) {
            $actual[] = $member instanceof NullableTypeNode;
        }

        self::assertSame($nullable, $actual);
    }

    public function testNullableListIsANullableMember(): void
    {
        $type = $this->parse('A&?B[]');

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertInstanceOf(NullableTypeNode::class, $type->types[1]);
        self::assertInstanceOf(TypesListTypeNode::class, $type->types[1]->type);
    }

    public function testTypeWithNoAmpersandIsNotAnIntersection(): void
    {
        self::assertNotInstanceOf(IntersectionTypeNode::class, $this->parse('A'));
        self::assertNotInstanceOf(IntersectionTypeNode::class, $this->parse('(A)'));
        self::assertNotInstanceOf(IntersectionTypeNode::class, $this->parse('?A'));
        self::assertNotInstanceOf(IntersectionTypeNode::class, $this->parse('A[]'));
        self::assertNotInstanceOf(IntersectionTypeNode::class, $this->parse('A /* & */'));
    }

    public function testIntersectionInsideOfAKeyIsTheKey(): void
    {
        $type = $this->parse('T[A&B]');

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->type);
        self::assertInstanceOf(IntersectionTypeNode::class, $type->access);
    }

    public function testIntersectionInsideOfAGroupBeforeASuffixIsTheElement(): void
    {
        $list = $this->parse('(A&B)[]');

        self::assertInstanceOf(TypesListTypeNode::class, $list);
        self::assertInstanceOf(IntersectionTypeNode::class, $list->type);

        $nullable = $this->parse('?(A&B)');

        self::assertInstanceOf(NullableTypeNode::class, $nullable);
        self::assertInstanceOf(IntersectionTypeNode::class, $nullable->type);
    }

    /**
     * An intersection begins where its first type begins, the types keep their
     * own offsets.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, list<int<0, max>>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['A&B', 0, [0, 2]];
        yield 'three' => ['A&B&C', 0, [0, 2, 4]];
        yield 'spaces before' => ['  A&B', 2, [2, 4]];
        yield 'spaces around' => ['A  &  B', 0, [0, 6]];
        yield 'line breaks' => ["A\n&\nB", 0, [0, 4]];
        yield 'comments' => ['A/* a */&/* b */B', 0, [0, 16]];
        yield 'multibyte comments' => ['/* Фу */ A /* Бар */ & B', 11, [11, 28]];
        yield 'nullable in a group on the left' => ['(?A)&B', 1, [1, 5]];
        yield 'nullable in a group on the right' => ['A&(?B)', 0, [0, 3]];
        yield 'list' => ['A[]&B[]', 0, [0, 4]];
        yield 'group on the left' => ['(A)&B', 1, [1, 4]];
        yield 'group of two' => ['(A)&(B)', 1, [1, 5]];
        yield 'many' => ['A &B& C', 0, [0, 3, 6]];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     * @param list<int<0, max>> $members
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetOfAnIntersectionIsTheOffsetOfItsFirstType(
        string $source,
        int $offset,
        array $members,
    ): void {
        $type = $this->parse($source);

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertSame($offset, $type->offset);

        $actual = [];

        foreach ($type->types as $member) {
            $actual[] = $member->offset;
        }

        // The offset of a nullable or of a list is the offset of its own sign
        self::assertSame($members, $actual);
    }

    public function testTriviaAroundTheIntersectionIsIgnored(): void
    {
        $type = $this->parse("/* a */ A /* b */ & // c\n B /* d */ // e\n");

        self::assertInstanceOf(IntersectionTypeNode::class, $type);
        self::assertCount(2, $type->types);
    }
}
