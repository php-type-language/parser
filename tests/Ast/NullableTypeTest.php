<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The question mark wraps the type behind it into a nullable.
 */
final class NullableTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>}>
     */
    public static function nullableDataProvider(): iterable
    {
        yield 'name' => ['?int', NamedTypeNode::class];
        yield 'qualified name' => ['?\\Foo\\Bar', NamedTypeNode::class];
        yield 'null' => ['?null', NullLiteralNode::class];
        yield 'int' => ['?42', IntLiteralNode::class];
        yield 'string' => ["?'a'", StringLiteralNode::class];
        yield 'this' => ['?$this', ThisTypeNode::class];
        yield 'class constant' => ['?Foo::BAR', ConstTypeNode::class];
        yield 'global mask' => ['?PHP_*', ConstMaskTypeNode::class];
        yield 'group' => ['?(int)', NamedTypeNode::class];
        yield 'list' => ['?int[]', TypesListTypeNode::class];
        yield 'access' => ['?T[K]', TypeOffsetAccessTypeNode::class];
        yield 'list of lists' => ['?int[][]', TypesListTypeNode::class];
        yield 'group of a list' => ['?(int[])', TypesListTypeNode::class];
        yield 'group of an access' => ['?(T[K])', TypeOffsetAccessTypeNode::class];
        yield 'group of a union' => ['?(A|B)', UnionTypeNode::class];
        yield 'group of an intersection' => ['?(A&B)', IntersectionTypeNode::class];
        yield 'group of a nullable' => ['?(?int)', NullableTypeNode::class];
        yield 'space' => ['? int', NamedTypeNode::class];
        yield 'line break' => ["?\nint", NamedTypeNode::class];
        yield 'comment' => ['?/* c */int', NamedTypeNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode> $inner
     */
    #[DataProvider('nullableDataProvider')]
    public function testNullableWrapsTheTypeBehindIt(string $source, string $inner): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(NullableTypeNode::class, $type);
        self::assertInstanceOf($inner, $type->type);
    }

    /**
     * The mark reaches over the suffixes behind it, the operators go over the nullable.
     */
    public function testOperatorsWrapTheNullable(): void
    {
        $union = $this->parse('?A|B');

        self::assertInstanceOf(UnionTypeNode::class, $union);
        self::assertInstanceOf(NullableTypeNode::class, $union->children[0]);
        self::assertInstanceOf(NamedTypeNode::class, $union->children[1]);

        $intersection = $this->parse('?A&B');

        self::assertInstanceOf(IntersectionTypeNode::class, $intersection);
        self::assertInstanceOf(NullableTypeNode::class, $intersection->children[0]);
        self::assertInstanceOf(NamedTypeNode::class, $intersection->children[1]);
    }

    public function testTypeWithNoMarkIsNotNullable(): void
    {
        self::assertNotInstanceOf(NullableTypeNode::class, $this->parse('int'));
        self::assertNotInstanceOf(NullableTypeNode::class, $this->parse('(int)'));
        self::assertNotInstanceOf(NullableTypeNode::class, $this->parse('int /* ? */'));
        self::assertNotInstanceOf(NullableTypeNode::class, $this->parse('int[]'));
        self::assertNotInstanceOf(NullableTypeNode::class, $this->parse('T[K]'));
    }

    public function testNullableIsKeptAsItIsWritten(): void
    {
        $type = $this->parse('?\\Some\\Any');

        self::assertInstanceOf(NullableTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->type);
        self::assertSame('\\Some\\Any', $type->type->name->toString());
    }

    public function testGroupBuildsNoNode(): void
    {
        $type = $this->parse('(?int)');

        self::assertInstanceOf(NullableTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->type);
    }

    /**
     * A nullable begins where its question mark stands, the type behind it
     * keeps its own offset.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['?int', 0, 1];
        yield 'spaces before' => ['  ?int', 2, 3];
        yield 'space behind the mark' => ['? int', 0, 2];
        yield 'spaces behind the mark' => ['?   int', 0, 4];
        yield 'line breaks behind the mark' => ["?\n\nint", 0, 3];
        yield 'comment behind the mark' => ['?/* c */int', 0, 8];
        yield 'multibyte comment behind the mark' => ['?/* Фу */ int', 0, 12];
        yield 'multibyte comment before the mark' => ['/* Фу */ ?int', 11, 12];
        yield 'group' => ['(?int)', 1, 2];
        yield 'group behind the mark' => ['?(int)', 0, 2];
        yield 'two groups' => ['((?int))', 2, 3];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $mark
     * @param int<0, max> $type
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetOfANullableIsTheOffsetOfItsMark(string $source, int $mark, int $type): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(NullableTypeNode::class, $node);
        self::assertSame($mark, $node->offset);
        self::assertSame($type, $node->type->offset);
    }

    public function testOffsetsOfNullablesInsideOfSuffixes(): void
    {
        // ?T[?K]: the mark of the outer one, the type, the mark of the key, the key
        $node = $this->parse('?T[?K]');

        self::assertInstanceOf(NullableTypeNode::class, $node);
        self::assertSame(0, $node->offset);

        $access = $node->type;

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $access);
        self::assertSame(1, $access->offset);
        self::assertSame(1, $access->type->offset);

        $key = $access->access;

        self::assertInstanceOf(NullableTypeNode::class, $key);
        self::assertSame(3, $key->offset);
        self::assertSame(4, $key->type->offset);
    }

    public function testNullableOfAListBeginsAtTheMarkAndTheListAtTheType(): void
    {
        $node = $this->parse('  ? int []');

        self::assertInstanceOf(NullableTypeNode::class, $node);
        self::assertSame(2, $node->offset);
        self::assertInstanceOf(TypesListTypeNode::class, $node->type);
        self::assertSame(4, $node->type->offset);
        self::assertSame(4, $node->type->type->offset);
    }

    public function testTriviaAroundTheNullableIsIgnored(): void
    {
        $type = $this->parse("/* a */ ? /* b */ int /* c */ // d\n");

        self::assertInstanceOf(NullableTypeNode::class, $type);
        self::assertInstanceOf(NamedTypeNode::class, $type->type);
    }
}
