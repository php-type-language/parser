<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The suffix `[K]` wraps the type before it, the type inside of the brackets is the key.
 */
final class OffsetAccessTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>, class-string<TypeNode>}>
     */
    public static function accessDataProvider(): iterable
    {
        yield 'name by a name' => ['T[K]', NamedTypeNode::class, NamedTypeNode::class];
        yield 'name by a string' => ["T['a']", NamedTypeNode::class, StringLiteralNode::class];
        yield 'name by an int' => ['T[0]', NamedTypeNode::class, IntLiteralNode::class];
        yield 'name by a negative int' => ['T[-1]', NamedTypeNode::class, IntLiteralNode::class];
        yield 'name by a constant' => ['T[Foo::A]', NamedTypeNode::class, ClassConstTypeNode::class];
        yield 'name by a list' => ['T[K[]]', NamedTypeNode::class, TypesListTypeNode::class];
        yield 'name by an access' => ['T[K[L]]', NamedTypeNode::class, TypeOffsetAccessTypeNode::class];
        yield 'name by a group' => ['T[(K)]', NamedTypeNode::class, NamedTypeNode::class];
        yield 'this by a name' => ['$this[K]', ThisTypeNode::class, NamedTypeNode::class];
        yield 'constant by a name' => ['Foo::BAR[K]', ClassConstTypeNode::class, NamedTypeNode::class];
        yield 'group by a name' => ['(T)[K]', NamedTypeNode::class, NamedTypeNode::class];
        yield 'spaces' => ['T [ K ]', NamedTypeNode::class, NamedTypeNode::class];
        yield 'line breaks' => ["T\n[\nK\n]", NamedTypeNode::class, NamedTypeNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode> $type
     * @param class-string<TypeNode> $access
     */
    #[DataProvider('accessDataProvider')]
    public function testAccessWrapsTheTypeBeforeItAndKeepsTheKey(string $source, string $type, string $access): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node);
        self::assertInstanceOf($type, $node->type);
        self::assertInstanceOf($access, $node->access);
    }

    public function testTypeAndKeyAreKeptAsTheyAreWritten(): void
    {
        $node = $this->parse("\\Some\\Any['key']");

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node);
        self::assertInstanceOf(NamedTypeNode::class, $node->type);
        self::assertSame('\\Some\\Any', $node->type->name->toString());
        self::assertInstanceOf(StringLiteralNode::class, $node->access);
        self::assertSame('key', $node->access->value);
    }

    /**
     * What a chain is made of, from the outer suffix to the inner one: `L` is a list, `A` is an access.
     *
     * @return iterable<non-empty-string, array{non-empty-string, string}>
     */
    public static function chainDataProvider(): iterable
    {
        yield 'access' => ['T[K]', 'A'];
        yield 'two accesses' => ['T[K][L]', 'AA'];
        yield 'access and list' => ['T[K][]', 'LA'];
        yield 'list and access' => ['T[][K]', 'AL'];
        yield 'mix' => ['T[][K][][L]', 'ALAL'];
        yield 'long chain' => ['T[A][B][C][D]', 'AAAA'];
        yield 'group of an access' => ['(T[K])[]', 'LA'];
        yield 'group of a list' => ['(T[])[K]', 'AL'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('chainDataProvider')]
    public function testSuffixesAreAppliedFromLeftToRight(string $source, string $expected): void
    {
        $node = $this->parse($source);
        $actual = '';

        while ($node instanceof TypeOffsetAccessTypeNode || $node instanceof TypesListTypeNode) {
            $actual .= $node instanceof TypeOffsetAccessTypeNode ? 'A' : 'L';
            $node = $node->type;
        }

        // The outer suffix is the last one written, so the chain read from outside is reversed
        self::assertSame($expected, $actual);
        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertSame('T', $node->name->toString());
    }

    public function testKeysOfAChainAreInTheOrderTheyAreWritten(): void
    {
        $node = $this->parse('T[A][B][C]');
        $keys = [];

        while ($node instanceof TypeOffsetAccessTypeNode) {
            \array_unshift($keys, $node->access->name->toString());
            $node = $node->type;
        }

        self::assertSame(['A', 'B', 'C'], $keys);
    }

    /**
     * Every access begins where the type before it begins, the key keeps its own offset.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>, int<0, max>, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['T[K]', 0, 0, 2];
        yield 'spaces before' => ['  T[K]', 2, 2, 4];
        yield 'spaces before the bracket' => ['T  [K]', 0, 0, 4];
        yield 'spaces inside' => ['T[  K  ]', 0, 0, 4];
        yield 'line breaks' => ["T\n[\nK\n]", 0, 0, 4];
        yield 'comments' => ['T/* a */[/* b */K]', 0, 0, 16];
        yield 'multibyte comments' => ['/* Фу */ T[/* Бар */ K]', 11, 11, 26];
        yield 'group' => ['(T)[K]', 1, 1, 4];
        yield 'group of the key' => ['T[(K)]', 0, 0, 3];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $access
     * @param int<0, max> $type
     * @param int<0, max> $key
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetOfAnAccessIsTheOffsetOfItsType(string $source, int $access, int $type, int $key): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node);
        self::assertSame($access, $node->offset);
        self::assertSame($type, $node->type->offset);
        self::assertSame($key, $node->access->offset);
    }

    public function testOffsetsOfAChainAreTheOffsetOfItsType(): void
    {
        $node = $this->parse('T[A][B] []');
        $offsets = [];

        while ($node instanceof TypeOffsetAccessTypeNode || $node instanceof TypesListTypeNode) {
            $offsets[] = $node->offset;
            $node = $node->type;
        }

        self::assertSame([0, 0, 0], $offsets);
    }

    public function testTriviaAroundTheAccessIsIgnored(): void
    {
        $node = $this->parse("/* a */ T /* b */ [ /* c */ K /* d */ ] // e\n");

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $node);
        self::assertInstanceOf(NamedTypeNode::class, $node->type);
        self::assertInstanceOf(NamedTypeNode::class, $node->access);
    }

    public function testTypeWithNoBracketsIsNotAnAccess(): void
    {
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('T'));
        self::assertInstanceOf(NamedTypeNode::class, $this->parse('T /* [K] */'));
        self::assertInstanceOf(TypesListTypeNode::class, $this->parse('T[]'));
    }
}
