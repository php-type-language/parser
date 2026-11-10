<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the way the `$this` is built in the places a type stands in.
 */
#[Group('unit'), Group('type-lang/parser')]
final class VariableTest extends AstTestCase
{
    public function testThisIsATypeOfItsOwn(): void
    {
        self::assertSame('ThisTypeNode', $this->parseAndPrint('$this'));
    }

    public function testThisStandsAsAReturnType(): void
    {
        self::assertSame(<<<'AST'
            Callable\CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
              ThisTypeNode
            AST, $this->parseAndPrint('callable(): $this'));
    }

    public function testThisStandsAsAParameterType(): void
    {
        self::assertSame(<<<'AST'
            Callable\CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false)
                  ThisTypeNode
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable($this): void'));
    }

    public function testThisIsATemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Some)
              Template\TemplateListNode
                Template\TypedTemplateArgumentNode
                  ThisTypeNode
            AST, $this->parseAndPrint('Some<$this>'));
    }

    public function testThisIsAShapeValue(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  ThisTypeNode
            AST, $this->parseAndPrint('array{a: $this}'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function wrappedThisDataProvider(): iterable
    {
        yield 'list' => ['$this[]', 'WrappingTypeNode\TypesListTypeNode'];
        yield 'nullable' => ['?$this', 'WrappingTypeNode\NullableTypeNode'];
        yield 'union' => ['$this|null', 'LogicalTypeNode\UnionTypeNode'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $wrapper
     * @throws \Throwable
     */
    #[DataProvider('wrappedThisDataProvider')]
    public function testThisIsWrappedLikeAnyOtherType(string $type, string $wrapper): void
    {
        self::assertStringStartsWith($wrapper, $this->parseAndPrint($type));
    }
}
