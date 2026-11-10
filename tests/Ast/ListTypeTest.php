<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The suffix `[]` wraps the type before it into a list.
 */
final class ListTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>}>
     */
    public static function listedDataProvider(): iterable
    {
        yield 'name' => ['int[]', NamedTypeNode::class];
        yield 'qualified name' => ['\\Foo\\Bar[]', NamedTypeNode::class];
        yield 'literal' => ['42[]', IntLiteralNode::class];
        yield 'this' => ['$this[]', ThisTypeNode::class];
        yield 'class constant' => ['Foo::BAR[]', ConstTypeNode::class];
        yield 'global mask' => ['PHP_*[]', ConstMaskTypeNode::class];
        yield 'group' => ['(int)[]', NamedTypeNode::class];
        yield 'spaces' => ['int [ ]', NamedTypeNode::class];
        yield 'line breaks' => ["int\n[\n]", NamedTypeNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode> $element
     */
    #[DataProvider('listedDataProvider')]
    public function testListWrapsTheTypeBeforeIt(string $source, string $element): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(TypesListTypeNode::class, $type);
        self::assertInstanceOf($element, $type->type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<1, max>}>
     */
    public static function chainDataProvider(): iterable
    {
        yield 'one' => ['int[]', 1];
        yield 'two' => ['int[][]', 2];
        yield 'two with spaces' => ['int [] []', 2];
        yield 'three' => ['int[][][]', 3];
        yield 'group of one and one' => ['(int[])[]', 2];
        yield 'group of two and one' => ['(int[][])[]', 3];
        yield 'two groups' => ['((int)[])[]', 2];
        yield 'twenty' => ['int' . \str_repeat('[]', 20), 20];
    }

    /**
     * @param non-empty-string $source
     * @param int<1, max> $depth
     */
    #[DataProvider('chainDataProvider')]
    public function testEachPairOfBracketsIsOneMoreList(string $source, int $depth): void
    {
        $type = $this->parse($source);
        $count = 0;

        while ($type instanceof TypesListTypeNode) {
            $type = $type->type;
            ++$count;
        }

        self::assertSame($depth, $count);
        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('int', $type->name->toString());
    }

    /**
     * Every list begins where the type under it begins, so all the lists of a
     * chain have the offset of the type. The offsets of the lists go from the
     * outer one to the inner one.
     *
     * @return iterable<non-empty-string, array{non-empty-string, list<int<0, max>>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['int[]', [0], 0];
        yield 'spaces before' => ['  int[]', [2], 2];
        yield 'spaces before the brackets' => ['int  []', [0], 0];
        yield 'comment before' => ['/* c */ int[]', [8], 8];
        yield 'multibyte before' => ['/* Фу */ int[]', [11], 11];
        yield 'group' => ['(int)[]', [1], 1];
        yield 'group with spaces' => ['(  int  ) []', [3], 3];
        yield 'two groups' => ['((int))[]', [2], 2];
        yield 'chain' => ['  int [][] []', [2, 2, 2], 2];
    }

    /**
     * @param non-empty-string $source
     * @param list<int<0, max>> $lists
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetOfEveryListIsTheOffsetOfItsType(string $source, array $lists, int $offset): void
    {
        $type = $this->parse($source);
        $actual = [];

        while ($type instanceof TypesListTypeNode) {
            $actual[] = $type->offset;
            $type = $type->type;
        }

        self::assertSame($lists, $actual);
        self::assertSame($offset, $type->offset);
    }

    public function testTriviaAroundTheListIsIgnored(): void
    {
        $type = $this->parse("/* a */ int /* b */ [ /* c */ ] // d\n");

        self::assertInstanceOf(TypesListTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->type);
    }

    public function testTypeWithNoBracketsIsNotAList(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('int'));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('(int)'));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('int /* [] */'));
    }
}
