<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName\NamespaceKind;

/**
 * A type that is a name and nothing more.
 */
final class NamedTypeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function nameDataProvider(): iterable
    {
        yield 'simple' => ['Foo'];
        yield 'builtin' => ['int'];
        yield 'hyphen' => ['non-empty-string'];
        yield 'multibyte' => ['Фу'];
        yield 'case is kept' => ['IS'];

        yield 'keyword is' => ['is'];
        yield 'keyword not' => ['not'];
        yield 'keyword of' => ['of'];
        yield 'keyword as' => ['as'];
        yield 'keyword super' => ['super'];
        yield 'keyword covariant' => ['covariant'];
        yield 'keyword contravariant' => ['contravariant'];
        yield 'keyword invariant' => ['invariant'];
        yield 'keyword with a hyphen' => ['is-x'];

        yield 'fully qualified' => ['\\Foo'];
        yield 'qualified' => ['Foo\\Bar'];
        yield 'fully qualified and qualified' => ['\\Foo\\Bar\\Baz'];
        yield 'keyword in a namespace' => ['Foo\\is\\of'];
        yield 'keyword leads a namespace' => ['is\\Foo'];

        yield 'fully qualified true' => ['\\true'];
        yield 'fully qualified false' => ['\\false'];
        yield 'fully qualified null' => ['\\NULL'];
        yield 'fully qualified literal word leads a namespace' => ['\\true\\Foo'];
        yield 'fully qualified literal word with a suffix' => ['\\truefoo'];
        yield 'literal word leads a namespace' => ['true\\null'];
        yield 'literal word ends a namespace' => ['Foo\\false'];
        yield 'literal word in the middle' => ['Foo\\null\\Bar'];
        yield 'literal word with a hyphen' => ['null-type'];
        yield 'literal word with a suffix' => ['trueFoo'];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('nameDataProvider')]
    public function testNameIsKeptAsItIsWritten(string $source): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame($source, $type->name->toString());
        self::assertNull($type->arguments);
        self::assertNull($type->fields);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['Foo', 0];
        yield 'spaces' => ['  Foo', 2];
        yield 'line break' => ["\n\nFoo", 2];
        yield 'block comment' => ['/* c */ Foo', 8];
        yield 'line comment' => ["// c\nFoo", 5];
        yield 'hash comment' => ["# c\nFoo", 4];
        yield 'qualified' => ['  Foo\\Bar', 2];
        yield 'before a root separator' => ['  \\Foo', 2];
        yield 'multibyte before' => ['/* Фу */ Foo', 11];
    }

    /**
     * The offset of a type is where its first character stands, the root separator included.
     *
     * @param non-empty-string $source
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetPointsToTheFirstCharacter(string $source, int $offset): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame($offset, $type->offset);
        self::assertSame($offset, $type->name->offset);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function triviaAroundDataProvider(): iterable
    {
        yield 'spaces' => ['  Foo  '];
        yield 'line breaks' => ["\r\nFoo\r\n"];
        yield 'comments' => ["/* a */ Foo // b\n"];
    }

    /**
     * @param non-empty-string $source
     */
    #[DataProvider('triviaAroundDataProvider')]
    public function testTriviaAroundIsIgnored(string $source): void
    {
        $type = $this->parse($source);

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('Foo', $type->name->toString());
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, class-string}>
     */
    public static function literalDataProvider(): iterable
    {
        yield 'true' => ['true', BoolLiteralNode::class];
        yield 'false' => ['false', BoolLiteralNode::class];
        yield 'null' => ['null', NullLiteralNode::class];
        yield 'true in upper case' => ['TRUE', BoolLiteralNode::class];
        yield 'null in mixed case' => ['NuLL', NullLiteralNode::class];
    }

    /**
     * @param non-empty-string $source
     * @param class-string $class
     */
    #[DataProvider('literalDataProvider')]
    public function testLoneReservedWordIsALiteral(string $source, string $class): void
    {
        self::assertInstanceOf($class, $this->parse($source));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, bool|null}>
     */
    public static function literalValueDataProvider(): iterable
    {
        yield 'true' => ['true', true];
        yield 'false' => ['false', false];
        yield 'null' => ['null', null];
    }

    /**
     * The leading separator is a part of the text, as it is written.
     *
     * @param non-empty-string $source
     */
    #[DataProvider('literalValueDataProvider')]
    public function testLiteralKeepsTheValueAndTheTextItIsWrittenAs(string $source, bool|null $value): void
    {
        $type = $this->parse("  $source");

        self::assertInstanceOf(LiteralNode::class, $type);
        self::assertSame($value, $type->value);
        self::assertSame($source, $type->raw);
        self::assertSame(2, $type->offset);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, string|null, NamespaceKind|null}>
     */
    public static function namespaceDataProvider(): iterable
    {
        yield 'no namespace' => ['Foo', 'Foo', null, null];
        yield 'qualified' => ['Some\Foo', 'Foo', 'Some', NamespaceKind::Qualified];
        yield 'qualified deeper' => ['Some\Any\Foo', 'Foo', 'Some\Any', NamespaceKind::Qualified];
        yield 'root' => ['\Foo', 'Foo', '', NamespaceKind::FullyQualified];
        yield 'fully qualified' => ['\Some\Foo', 'Foo', 'Some', NamespaceKind::FullyQualified];
        yield 'fully qualified deeper' => ['\Some\Any\Foo', 'Foo', 'Some\Any', NamespaceKind::FullyQualified];
        yield 'current namespace' => ['namespace\Foo', 'Foo', '', NamespaceKind::NamespaceRelative];
        yield 'relative to the current' => ['namespace\Some\Foo', 'Foo', 'Some', NamespaceKind::NamespaceRelative];
        yield 'keyword in upper case' => ['NAMESPACE\Foo', 'Foo', '', NamespaceKind::NamespaceRelative];
        yield 'keyword as a word' => ['Some\namespace\Foo', 'Foo', 'Some\namespace', NamespaceKind::Qualified];
        yield 'virtual name' => ['non-empty-string', 'non-empty-string', null, null];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $word
     */
    #[DataProvider('namespaceDataProvider')]
    public function testNameIsCutIntoNamespaceAndWord(
        string $source,
        string $word,
        ?string $value,
        ?NamespaceKind $kind,
    ): void {
        $type = $this->parse("  $source");

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame($word, $type->name->value);
        self::assertSame($value, $type->name->namespace?->value);
        self::assertSame($kind, $type->name->namespace?->kind);
        self::assertSame(2, $type->name->offset);
        self::assertSame($kind === null ? null : 2, $type->name->namespace?->offset);
    }
}
