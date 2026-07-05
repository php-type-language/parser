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
        self::assertTrue($statement->name->isSimple);
    }

    public function testRelativeNamespacedName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Example\Name)
                Identifier(Example)
                Identifier(Name)
            AST, $this->parseAndPrint('Example\\Name'));
    }

    public function testAbsoluteNamespacedName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(\Absolute\Type\Name)
                Identifier(Absolute)
                Identifier(Type)
                Identifier(Name)
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
                Identifier({$keyword})
            AST, $this->parseAndPrint('\\' . $keyword));
    }

    public function testNameCannotStartWithDigit(): void
    {
        $this->expectParsingException('unexpected "invalid_name_0"');

        $this->parse('0_invalid_name_0');
    }

    public function testNameCannotStartWithDigitFollowedByLetters(): void
    {
        $this->expectParsingException('unexpected "type"');

        $this->parse('42type');
    }

    public function testNameCannotStartWithDash(): void
    {
        $this->expectParsingException('unexpected "-"');

        $this->parse('-foo');
    }

    public function testNamespaceCannotEndWithDelimiter(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('example\\name\\');
    }

    public function testNamespaceCannotContainStandaloneKeyword(): void
    {
        $this->expectParsingException('unexpected "\\"');

        $this->parse('true\\null');
    }
}
