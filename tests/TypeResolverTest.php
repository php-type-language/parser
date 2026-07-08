<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\TypeResolver;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;

/**
 * Tests for {@see TypeResolver} that expands short type names into their
 * fully-qualified form using registered `use`-like imports and aliases.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TypeResolverTest extends TestCase
{
    /**
     * Resolves the given source type and returns the name of the
     * top-level {@see NamedTypeNode}.
     *
     * @throws \Throwable
     */
    private function resolveName(TypeResolver $resolver, string $code): string
    {
        $node = $resolver->resolve($this->parse($code));

        self::assertInstanceOf(NamedTypeNode::class, $node);

        return $node->name->toString();
    }

    public function testResolvesImportedName(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        self::assertSame(
            'TypeLang\Parser\Node',
            $this->resolveName($resolver, 'Node'),
        );
    }

    public function testResolvesImportedNamePrefix(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        self::assertSame(
            'TypeLang\Parser\Node\Foo',
            $this->resolveName($resolver, 'Node\Foo'),
        );
    }

    public function testResolvesAliasedName(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame(
            'TypeLang\Parser\Exception\SemanticException',
            $this->resolveName($resolver, 'Error\SemanticException'),
        );
    }

    public function testResolvesAliasItself(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame(
            'TypeLang\Parser\Exception',
            $this->resolveName($resolver, 'Error'),
        );
    }

    /**
     * The first segment of a name is matched case-insensitively against
     * the registered imports.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function caseInsensitiveDataProvider(): iterable
    {
        yield 'exact' => ['Node', 'TypeLang\Parser\Node'];
        yield 'lower' => ['node', 'TypeLang\Parser\Node'];
        yield 'upper' => ['NODE', 'TypeLang\Parser\Node'];
        yield 'lower with prefix' => ['node\Foo', 'TypeLang\Parser\Node\Foo'];
        yield 'upper with prefix' => ['NODE\Foo', 'TypeLang\Parser\Node\Foo'];
    }

    #[DataProvider('caseInsensitiveDataProvider')]
    public function testMatchesFirstSegmentCaseInsensitively(string $code, string $expected): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        self::assertSame($expected, $this->resolveName($resolver, $code));
    }

    public function testLeavesUnknownNameUnchanged(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        self::assertSame('Unknown', $this->resolveName($resolver, 'Unknown'));
    }

    public function testEmptyResolverLeavesNameUnchanged(): void
    {
        self::assertSame(
            'Node\Foo',
            $this->resolveName(new TypeResolver(), 'Node\Foo'),
        );
    }

    public function testBuiltinTypeIsLeftUnchanged(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        self::assertSame('int', $this->resolveName($resolver, 'int'));
    }

    public function testLastImportWinsForDuplicateLastSegment(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('First\Node')
            ->withTypeImport('Second\Node');

        self::assertSame('Second\Node', $this->resolveName($resolver, 'Node'));
    }

    public function testResolvesNamesInsideUnion(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node')
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame(<<<'AST'
            UnionTypeNode
              NamedTypeNode
                Name(TypeLang\Parser\Node)
                  Identifier(TypeLang)
                  Identifier(Parser)
                  Identifier(Node)
              NamedTypeNode
                Name(TypeLang\Parser\Exception)
                  Identifier(TypeLang)
                  Identifier(Parser)
                  Identifier(Exception)
            AST, $this->print($resolver->resolve($this->parse('Node|Error'))));
    }

    public function testResolvesNamesInsideShape(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node')
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\ImplicitFieldNode(required)
                  NamedTypeNode
                    Name(TypeLang\Parser\Node)
                      Identifier(TypeLang)
                      Identifier(Parser)
                      Identifier(Node)
                Shape\ImplicitFieldNode(required)
                  NamedTypeNode
                    Name(TypeLang\Parser\Exception\SemanticException)
                      Identifier(TypeLang)
                      Identifier(Parser)
                      Identifier(Exception)
                      Identifier(SemanticException)
            AST, $this->print($resolver->resolve($this->parse('array{Node, Error\SemanticException}'))));
    }

    public function testResolvesNamesInsideGenericArguments(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('Vendor\Collection')
            ->withTypeImport('Vendor\Node');

        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Vendor\Collection)
                Identifier(Vendor)
                Identifier(Collection)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Vendor\Node)
                      Identifier(Vendor)
                      Identifier(Node)
            AST, $this->print($resolver->resolve($this->parse('Collection<Node>'))));
    }

    public function testResolveMutatesInPlaceAndReturnsSameInstance(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        $source = $this->parse('Node');
        $result = $resolver->resolve($source);

        self::assertSame($source, $result);
        self::assertInstanceOf(NamedTypeNode::class, $result);
        self::assertSame('TypeLang\Parser\Node', $result->name->toString());
    }

    public function testWithTypeImportDoesNotMutateSourceResolver(): void
    {
        $base = new TypeResolver();
        $derived = $base->withTypeImport('TypeLang\Parser\Node');

        self::assertNotSame($base, $derived);
        self::assertSame('Node', $this->resolveName($base, 'Node'));
        self::assertSame('TypeLang\Parser\Node', $this->resolveName($derived, 'Node'));
    }

    public function testWithTypeImportAsDoesNotMutateSourceResolver(): void
    {
        $base = new TypeResolver();
        $derived = $base->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertNotSame($base, $derived);
        self::assertSame('Error', $this->resolveName($base, 'Error'));
        self::assertSame('TypeLang\Parser\Exception', $this->resolveName($derived, 'Error'));
    }
}
