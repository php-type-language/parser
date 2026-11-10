<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;

/**
 * The parentheses group a type and build no node of their own.
 */
final class GroupTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string<TypeNode>}>
     */
    public static function groupedDataProvider(): iterable
    {
        yield 'name' => ['(int)', NamedTypeNode::class];
        yield 'two pairs' => ['((int))', NamedTypeNode::class];
        yield 'qualified name' => ['(\\Foo\\Bar)', NamedTypeNode::class];
        yield 'literal' => ['(42)', IntLiteralNode::class];
        yield 'this' => ['($this)', ThisTypeNode::class];
        yield 'class constant' => ['(Foo::BAR)', ClassConstTypeNode::class];
        yield 'global mask' => ['(PHP_*)', ConstMaskTypeNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string<TypeNode> $class
     */
    #[DataProvider('groupedDataProvider')]
    public function testGroupIsTheTypeInsideOfIt(string $source, string $class): void
    {
        self::assertInstanceOf($class, $this->parse($source));
    }

    public function testGroupKeepsTheTypeAsItIsWritten(): void
    {
        $type = $this->parse('((\\Foo\\Bar))');

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('\\Foo\\Bar', $type->name->toString());
        self::assertNull($type->arguments);
        self::assertNull($type->fields);
    }

    public function testTriviaAroundTheGroupIsIgnored(): void
    {
        $type = $this->parse("/* a */ ( /* b */ int /* c */ ) // d\n");

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('int', $type->name->toString());
    }

    /**
     * The offset of a type is where the type begins, the parentheses are not a part of it.
     *
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'one pair' => ['(int)', 1];
        yield 'two pairs' => ['((int))', 2];
        yield 'spaces inside' => ['(  int  )', 3];
        yield 'spaces around' => ['  ( int )', 4];
        yield 'comment inside' => ['(/* c */ int)', 9];
        yield 'multibyte before' => ['(/* Фу */ int)', 12];
        yield 'twenty pairs' => [\str_repeat('(', 20) . 'int' . \str_repeat(')', 20), 20];
    }

    /**
     * @param non-empty-string $source
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetPointsToTheTypeNotToTheParentheses(string $source, int $offset): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame($offset, $type->offset);
        self::assertSame($offset, $type->name->offset);
    }
}
