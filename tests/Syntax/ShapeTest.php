<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for shape (structural) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ShapeTest extends SyntaxTestCase
{
    public function testNamedExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(required)
                  Identifier(a)
                  NamedTypeNode
                    Name(first)
                      Identifier(first)
                Shape\NamedFieldNode(required)
                  Identifier(b)
                  NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{a: first, b: second}'));
    }

    public function testNumericExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\NumericFieldNode(required)
                  Literal\IntLiteralNode(1)
                  NamedTypeNode
                    Name(first)
                      Identifier(first)
                Shape\NumericFieldNode(required)
                  Literal\IntLiteralNode(42)
                  NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{1: first, 42: second}'));
    }

    public function testStringExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\StringNamedFieldNode(required)
                  Literal\StringLiteralNode("name-some")
                  NamedTypeNode
                    Name(first)
                      Identifier(first)
                Shape\StringNamedFieldNode(required)
                  Literal\StringLiteralNode("escape\nchars")
                  NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{"name-some": first, "escape\\nchars": second}'));
    }

    public function testImplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\ImplicitFieldNode(required)
                  NamedTypeNode
                    Name(first)
                      Identifier(first)
                Shape\ImplicitFieldNode(required)
                  NamedTypeNode
                    Name(second)
                      Identifier(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(required)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
                      Identifier(int)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(optional)
                  Identifier(key)
                  NamedTypeNode
                    Name(Type)
                      Identifier(Type)
            AST, $this->parseAndPrint('array{key?: Type}'));
    }

    public function testUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(unsealed)
                Shape\NamedFieldNode(required)
                  Identifier(key)
                  NamedTypeNode
                    Name(type)
                      Identifier(type)
            AST, $this->parseAndPrint('array{key: type, ...}'));
    }

    public function testTypedUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(string)
                      Identifier(string)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(object)
                      Identifier(object)
              Shape\FieldsListNode(unsealed)
                Shape\NamedFieldNode(required)
                  Identifier(user)
                  NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('array{user: User, ...<string, object>}'));
    }

    public function testShapeOnArbitraryTypeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(App\Domain\User)
                Identifier(App)
                Identifier(Domain)
                Identifier(User)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(required)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
                      Identifier(non-empty-string)
            AST, $this->parseAndPrint('App\\Domain\\User{userName: non-empty-string}'));
    }

    public function testCannotMixExplicitAndImplicitKeys(): void
    {
        $this->expectParsingException('Cannot mix explicit and implicit shape keys');

        $this->parse('array{named: first, second}');
    }

    public function testDuplicateKeyIsNotAllowed(): void
    {
        $this->expectParsingException('Duplicate key "a"');

        $this->parse('array{a: int, a: string}');
    }

    public function testOptionalValueSyntaxIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "?"');

        $this->parse('array{key: Type?}');
    }
}
