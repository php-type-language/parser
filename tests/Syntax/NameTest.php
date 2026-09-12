<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\NamedTypeNode;

/**
 * Tests for the grammar of type names (identifiers) and namespaces.
 */
#[Group('unit'), Group('type-lang/parser')]
final class NameTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function validNamesDataProvider(): iterable
    {
        yield 'simple' => ['ExampleTypeName', 'ExampleTypeName'];
        yield 'leading underscore' => ['_foo', '_foo'];
        yield 'with underscore' => ['Foo_Bar', 'Foo_Bar'];
        yield 'with digits' => ['foo123', 'foo123'];
        yield 'dash in the middle' => ['example-type', 'example-type'];
        yield 'double dash' => ['a--b', 'a--b'];
        yield 'trailing dash' => ['Foo-', 'Foo-'];
        yield 'virtual builtin' => ['non-empty-string', 'non-empty-string'];
        yield 'reserved keyword as part' => ['true-type', 'true-type'];
        yield 'reserved false as part' => ['false-type', 'false-type'];
        yield 'reserved null as part' => ['null-type', 'null-type'];
    }

    #[DataProvider('validNamesDataProvider')]
    public function testValidNames(string $type, string $expected): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(NamedTypeNode::class, $statement);
        self::assertSame($expected, $statement->name->toString());
        self::assertTrue($statement->name->isSimple());
    }

    public function testRelativeNamespacedName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Example\Name)
            AST, $this->parseAndPrint('Example\\Name'));
    }

    public function testAbsoluteNamespacedName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(\Absolute\Type\Name)
            AST, $this->parseAndPrint('\\Absolute\\Type\\Name'));
    }

    /**
     * The namespace delimiter can be used in conjunction with keywords such as
     * "true", "false", or "null" to explicitly indicate a type reference.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function keywordReferenceDataProvider(): iterable
    {
        yield 'true' => ['true'];
        yield 'false' => ['false'];
        yield 'null' => ['null'];
    }

    #[DataProvider('keywordReferenceDataProvider')]
    public function testKeywordAsExplicitTypeReference(string $keyword): void
    {
        self::assertSame(<<<AST
            NamedTypeNode
              Name(\\{$keyword})
            AST, $this->parseAndPrint('\\' . $keyword));
    }

    /**
     * A source text is read as bytes, and every byte of a character outside
     * of ASCII is a letter, so a name stands in whatever script it is
     * written in.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function nonAsciiNameDataProvider(): iterable
    {
        yield 'latin-1' => ['Über', 'Über'];
        yield 'cyrillic' => ['Тип', 'Тип'];
        yield 'cjk' => ['你好', '你好'];
        yield 'a namespaced name' => ['Проект\Тип', 'Проект\Тип'];
        yield 'beside an ascii part' => ['Проект\Type', 'Проект\Type'];
        yield 'with a dash' => ['non-empty-Тип', 'non-empty-Тип'];
        yield 'with a digit' => ['Тип42', 'Тип42'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $expected
     * @throws \Throwable
     */
    #[DataProvider('nonAsciiNameDataProvider')]
    public function testANameStandsInAnyScript(string $type, string $expected): void
    {
        self::assertSame(
            $expected,
            (new \TypeLang\Printer\PrettyTypePrinter())->print($this->parse($type)),
        );
    }

    public function testANonAsciiNameStandsAsAShapeKey(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(ключ)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint('array{ключ: int}'));
    }

    public function testANonAsciiNameStandsAsATemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(list)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Тип)
            AST, $this->parseAndPrint('list<Тип>'));
    }
    public function testNameCannotStartWithDigit(): void
    {
        // The underscore belongs to the name, not to the number
        $this->expectParsingException('unexpected "_invalid_name_0"');

        $this->parse('0_invalid_name_0');
    }

    public function testNameCannotStartWithDigitFollowedByLetters(): void
    {
        $this->expectParsingException('unexpected "type"');

        $this->parse('42type');
    }

    public function testNameCannotStartWithDash(): void
    {
        $this->expectParsingException('unexpected "-foo"');

        $this->parse('-foo');
    }

    public function testNamespaceCannotEndWithDelimiter(): void
    {
        $this->expectParsingException('a name must carry a segment after the separator');

        $this->parse('example\\name\\');
    }

    public function testNamespaceCannotContainStandaloneKeyword(): void
    {
        $this->expectParsingException('unexpected "\\"');

        $this->parse('true\\null');
    }
}
