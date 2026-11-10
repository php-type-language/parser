<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for shape (structural) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ShapeTest extends AstTestCase
{
    protected function setUp(): void
    {
        // Enabled for step 13
    }

    public function testNamedExplicitKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
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
              Shape\FieldListNode(isSealed=true)
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
              Shape\FieldListNode(isSealed=true)
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
              Shape\FieldListNode(isSealed=true)
                Shape\ImplicitFieldNode
                  NamedTypeNode
                    Name(first)
                Shape\ImplicitFieldNode
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{first, second}'));
    }

    public function testEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
            AST, $this->parseAndPrint('array{}'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint('array{a: int,}'));
    }

    /**
     * A trailing comma follows a field or an ellipsis, and an empty shape
     * carries neither.
     */
    public function testTrailingCommaOfEmptyShapeIsNotAllowed(): void
    {
        $this->expectParsingException();

        $this->parse('array{,}');
    }

    public function testOptionalKey(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
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
              Shape\FieldListNode(isSealed=false)
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
              Template\TemplateListNode
                Template\TypedTemplateArgumentNode
                  NamedTypeNode
                    Name(string)
                Template\TypedTemplateArgumentNode
                  NamedTypeNode
                    Name(object)
              Shape\FieldListNode(isSealed=false)
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
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
            AST, $this->parseAndPrint('App\\Domain\\User{userName: non-empty-string}'));
    }

    /**
     * Explicit and implicit fields may be written together, the way PHPStan
     * and Psalm read such a shape.
     */
    public function testExplicitAndImplicitKeysMayBeMixed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(named)
                  NamedTypeNode
                    Name(first)
                Shape\ImplicitFieldNode
                  NamedTypeNode
                    Name(second)
            AST, $this->parseAndPrint('array{named: first, second}'));
    }

    public function testImplicitKeyMayPrecedeExplicitOne(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\ImplicitFieldNode
                  NamedTypeNode
                    Name(int)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(string)
            AST, $this->parseAndPrint('array{int, a: string}'));
    }

    /**
     * A key may be written more than once, the way PHPStan and Psalm read
     * such a shape: every field stays in the tree as it is written.
     */
    public function testDuplicateKeyIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(string)
            AST, $this->parseAndPrint('array{a: int, a: string}'));
    }

    /**
     * Keys of different forms denoting the same key are written apart as well.
     */
    public function testDuplicateKeyOfDifferentFormsIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\IntLiteralNode(1)
                  NamedTypeNode
                    Name(int)
                Shape\ScalarFieldNode(isOptional=false)
                  Literal\StringLiteralNode("1")
                  NamedTypeNode
                    Name(string)
            AST, $this->parseAndPrint('array{1: int, "1": string}'));
    }

    public function testOptionalValueSyntaxIsNotAllowed(): void
    {
        $this->expectParsingException();

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
        $this->expectParsingException();

        $this->parse($type);
    }

    public function testConstantKeys(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldListNode(isSealed=true)
                Shape\ComplexFieldNode(isOptional=false)
                  Constant\ConstTypeNode
                    Name(Foo)
                    Identifier(BAR)
                  NamedTypeNode
                    Name(int)
                Shape\ComplexFieldNode(isOptional=true)
                  Constant\ConstTypeNode
                    Name(\Foo\Bar)
                    Identifier(BAZ)
                  NamedTypeNode
                    Name(string)
                Shape\ComplexFieldNode(isOptional=false)
                  Constant\ConstTypeNode
                    Name(Foo)
                    Identifier(class)
                  NamedTypeNode
                    Name(bool)
            AST, $this->parseAndPrint('array{Foo::BAR: int, \Foo\Bar::BAZ?: string, Foo::class: bool}'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function maskKeyDataProvider(): iterable
    {
        yield 'global mask' => ['array{PHP_*: int}'];
        yield 'namespace mask' => ['array{Foo\\BAR_*: int}'];
        yield 'class constant mask' => ['array{Foo::BAR_*: int}'];
        yield 'class lone star mask' => ['array{Foo::*: int}'];
        yield 'optional mask' => ['array{PHP_*?: int}'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('maskKeyDataProvider')]
    public function testMaskKeysAreRejected(string $source): void
    {
        $this->expectParsingException();

        $this->parse($source);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, bool}>
     */
    public static function qualifiedKeyDataProvider(): iterable
    {
        yield 'qualified' => ['array{Foo\\Bar: int}', 'Foo\\Bar', false];
        yield 'fully qualified' => ['array{\\Foo: int}', '\\Foo', false];
        yield 'qualified constant' => ['array{Foo\\BAR: int}', 'Foo\\BAR', false];
        yield 'fully qualified constant' => ['array{\\Foo\\BAR: int}', '\\Foo\\BAR', false];
        yield 'optional' => ['array{Foo\\Bar?: int}', 'Foo\\Bar', true];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $key
     */
    #[DataProvider('qualifiedKeyDataProvider')]
    public function testQualifiedNameKeys(string $source, string $key, bool $isOptional): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->fields);

        $field = $node->fields[0];

        self::assertInstanceOf(\TypeLang\Type\Shape\NamedFieldNode::class, $field);
        self::assertInstanceOf(\TypeLang\Type\Identifier::class, $field->key);
        self::assertSame($key, $field->key->toString());
        self::assertSame($isOptional, $field->isOptional);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function signedIntKeyDataProvider(): iterable
    {
        yield 'negative' => ['array{-1: int}', -1];
        yield 'negative with a space' => ['array{- 1: int}', -1];
        yield 'positive' => ['array{+1: int}', 1];
        yield 'positive with a space' => ['array{+ 1: int}', 1];
        yield 'unsigned' => ['array{1: int}', 1];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('signedIntKeyDataProvider')]
    public function testSignedIntegerKeys(string $source, int $key): void
    {
        $node = $this->parse($source);

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->fields);

        $field = $node->fields[0];

        self::assertInstanceOf(\TypeLang\Type\Shape\ScalarFieldNode::class, $field);
        self::assertInstanceOf(\TypeLang\Type\Literal\IntLiteralNode::class, $field->key);
        self::assertSame($key, $field->key->value);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function floatKeyDataProvider(): iterable
    {
        yield 'float' => ['array{1.5: int}'];
        yield 'negative float' => ['array{-1.5: int}'];
        yield 'negative float with a space' => ['array{- 1.5: int}'];
        yield 'positive float with a space' => ['array{+ 1.5: int}'];
        yield 'float with an exponent' => ['array{1e3: int}'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('floatKeyDataProvider')]
    public function testFloatKeysAreRejected(string $source): void
    {
        $this->expectParsingException();

        $this->parse($source);
    }

    public function testOffsets(): void
    {
        $node = $this->parse('  array{a: int, 0?: string, Foo::BAR: bool, ...<K, V>}  ');

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertSame(2, $node->offset);
        self::assertSame(2, $node->name->offset);

        self::assertNotNull($node->fields);
        self::assertSame(8, $node->fields->offset);
        self::assertFalse($node->fields->isSealed);
        self::assertCount(3, $node->fields);

        // a: int
        $f0 = $node->fields[0];
        self::assertInstanceOf(\TypeLang\Type\Shape\NamedFieldNode::class, $f0);
        self::assertSame(8, $f0->offset);
        self::assertSame(8, $f0->key->offset);
        self::assertSame('a', $f0->key->toString());
        self::assertSame(11, $f0->type->offset);
        self::assertFalse($f0->isOptional);

        // 0?: string
        $f1 = $node->fields[1];
        self::assertInstanceOf(\TypeLang\Type\Shape\ScalarFieldNode::class, $f1);
        self::assertSame(16, $f1->offset);
        self::assertSame(16, $f1->key->offset);
        self::assertSame(20, $f1->type->offset);
        self::assertTrue($f1->isOptional);

        // Foo::BAR: bool
        $f2 = $node->fields[2];
        self::assertInstanceOf(\TypeLang\Type\Shape\ComplexFieldNode::class, $f2);
        self::assertSame(28, $f2->offset);
        self::assertSame(28, $f2->key->offset);
        self::assertSame(38, $f2->type->offset);
        self::assertFalse($f2->isOptional);

        // ...<K, V>
        self::assertNotNull($node->arguments);
        self::assertSame(48, $node->arguments->offset);
        self::assertCount(2, $node->arguments);
    }

    public function testDisabledShapes(): void
    {
        $this->expectFeatureException('Shapes feature is not allowed');

        $this->parse('array{a: int}', ['shapes' => false]);
    }

    public function testDisabledGenericsInUnsealedShape(): void
    {
        $this->expectFeatureException('Generics feature is not allowed');

        $this->parse('array{a: int, ...<K, V>}', ['generics' => false]);
    }

    /**
     * A key ends in the ":" its value begins after, so a type that carries
     * a colon of its own is no key.
     */
    public function testKeyDoesNotReachBeyondAPrimaryType(): void
    {
        $this->expectParsingException();

        $this->parse('array{T is A ? B : C: int}');
    }
}
