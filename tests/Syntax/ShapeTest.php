<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
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
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(first)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(b)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{a: first, b: second}'));
    }

    public function testNumericExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\IntLiteralNode(1)
                  NamedTypeNode
                    Name(first)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\IntLiteralNode(42)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{1: first, 42: second}'));
    }

    public function testStringExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\StringLiteralNode("name-some")
                  NamedTypeNode
                    Name(first)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\StringLiteralNode("escape\nchars")
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{"name-some": first, "escape\\nchars": second}'));
    }

    public function testImplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\ImplicitFieldNode(isOptional=false)
                  NamedTypeNode
                    Name(first)
                Shape\ImplicitFieldNode(isOptional=false)
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=true)
                  Identifier(key)
                  NamedTypeNode
                    Name(Type)
            AST, $this->parseAndPrint('array{key?: Type}'));
    }

    public function testUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=false)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(key)
                  NamedTypeNode
                    Name(type)
            AST, $this->parseAndPrint('array{key: type, ...}'));
    }

    public function testTypedUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(string)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(object)
              Shape\FieldsListNode(isSealed=false)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(user)
                  NamedTypeNode
                    Name(User)
            AST, $this->parseAndPrint('array{user: User, ...<string, object>}'));
    }

    public function testShapeOnArbitraryTypeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(App\Domain\User)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
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
        $this->expectParsingException('a shape must be closed with a brace "}"');

        $this->parse('array{key: Type?}');
    }

    /**
     * A "true" and a "null" name a field as the words they are written with,
     * not as the values they name elsewhere.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function keywordKeyDataProvider(): iterable
    {
        yield 'true' => ['array{true: int}'];
        yield 'false' => ['array{false: int}'];
        yield 'null' => ['array{null: int}'];
    }

    #[DataProvider('keywordKeyDataProvider')]
    public function testKeywordKeyIsAName(string $type): void
    {
        self::assertSame($type, (new \TypeLang\Printer\PrettyTypePrinter())->print($this->parse($type)));
    }

    /**
     * A key is a number or a string and nothing else, the way a key of an
     * array is.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function invalidKeyDataProvider(): iterable
    {
        yield 'boolean' => ['array{(true): int}'];
        yield 'boolean of the other kind' => ['array{(false): int}'];
        yield 'null' => ['array{(null): int}'];
        yield 'float' => ['array{0.42: int}'];
        yield 'float in parentheses' => ['array{(0.42): int}'];
        yield 'variable' => ['array{$this: int}'];
        yield 'union' => ['array{(A|B): int}'];
    }

    #[DataProvider('invalidKeyDataProvider')]
    public function testKeyIsANumberAStringOrANameAlone(string $type): void
    {
        $this->expectParsingException('Shape key must be a name, a number, a string');

        $this->parse($type);
    }

    /**
     * A key ends in the ":" its value begins after, so a type that carries
     * a colon of its own is no key.
     */
    public function testKeyDoesNotReachBeyondAPrimaryType(): void
    {
        $this->expectParsingException('a shape must be closed with a brace "}"');

        $this->parse('array{T is A ? B : C: int}');
    }
}
