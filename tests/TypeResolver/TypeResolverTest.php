<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\TypeResolver;
use TypeLang\Type\NamedTypeNode;

/**
 * Tests for {@see TypeResolver} that expands short type names into their
 * fully-qualified form using registered `use`-like imports and aliases.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TypeResolverTest extends TypeResolverTestCase
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

    public function testResolvesImportsPassedThroughConstructor(): void
    {
        $resolver = new TypeResolver([
            'TypeLang\Parser\Node',
            'Error' => 'TypeLang\Parser\Exception',
        ]);

        self::assertSame('TypeLang\Parser\Node', $this->resolveName($resolver, 'Node'));
        self::assertSame(
            'TypeLang\Parser\Exception\SemanticException',
            $this->resolveName($resolver, 'Error\SemanticException'),
        );
    }

    /**
     * An alias, like a real `use ... as`, is matched case-insensitively.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function aliasCaseDataProvider(): iterable
    {
        yield 'exact' => ['Error\Sub', 'TypeLang\Parser\Exception\Sub'];
        yield 'lower' => ['error\Sub', 'TypeLang\Parser\Exception\Sub'];
        yield 'upper' => ['ERROR\Sub', 'TypeLang\Parser\Exception\Sub'];
    }

    #[DataProvider('aliasCaseDataProvider')]
    public function testMatchesAliasCaseInsensitively(string $code, string $expected): void
    {
        $resolver = new TypeResolver()
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame($expected, $this->resolveName($resolver, $code));
    }

    public function testResolvesNamesInsideCallableType(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node')
            ->withTypeImportAs('App\Err\Exception', 'Error');

        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
                Identifier(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(App\Node)
                      Identifier(App)
                      Identifier(Node)
              NamedTypeNode
                Name(App\Err\Exception)
                  Identifier(App)
                  Identifier(Err)
                  Identifier(Exception)
            AST, $this->print($resolver->resolve($this->parse('callable(Node): Error'))));
    }

    public function testResolvesClassInClassConstant(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node');

        self::assertSame(<<<'AST'
            ClassConstNode
              Name(App\Node)
                Identifier(App)
                Identifier(Node)
              Identifier(FOO)
            AST, $this->print($resolver->resolve($this->parse('Node::FOO'))));
    }

    public function testResolvesClassInClassConstantMask(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node');

        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(App\Node)
                Identifier(App)
                Identifier(Node)
            AST, $this->print($resolver->resolve($this->parse('Node::*'))));
    }

    public function testResolvesNamesInsideIntersection(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node')
            ->withTypeImportAs('App\Err\Exception', 'Error');

        self::assertSame(<<<'AST'
            IntersectionTypeNode
              NamedTypeNode
                Name(App\Node)
                  Identifier(App)
                  Identifier(Node)
              NamedTypeNode
                Name(App\Err\Exception)
                  Identifier(App)
                  Identifier(Err)
                  Identifier(Exception)
            AST, $this->print($resolver->resolve($this->parse('Node&Error'))));
    }

    public function testResolvesNamesInsideConditional(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node')
            ->withTypeImportAs('App\Err\Exception', 'Error');

        self::assertSame(<<<'AST'
            TernaryExpressionNode
              Condition\EqualConditionNode
                NamedTypeNode
                  Name(App\Node)
                    Identifier(App)
                    Identifier(Node)
                NamedTypeNode
                  Name(App\Err\Exception)
                    Identifier(App)
                    Identifier(Err)
                    Identifier(Exception)
              Literal\BoolLiteralNode(true)
              Literal\BoolLiteralNode(false)
            AST, $this->print($resolver->resolve($this->parse('Node is Error ? true : false'))));
    }

    /**
     * A name prefixed with the `namespace` keyword is relative to the current
     * namespace, not to a `use` import, so it must never be substituted.
     */
    public function testDoesNotResolveNamespaceRelativePrefix(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node');

        self::assertSame('namespace\Node', $this->resolveName($resolver, 'namespace\Node'));
    }

    /**
     * Only the first segment is matched against the import table; a name whose
     * first segment differs is left untouched even when a later segment equals
     * an alias.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function nonMatchingNameDataProvider(): iterable
    {
        yield 'unrelated root' => ['Other\Node'];
        yield 'alias buried in tail' => ['Some\Error\Thing'];
        // The fully-qualified target of the import itself starts with `App`,
        // not with the `Node` alias, so re-resolving it is a no-op.
        yield 'already written out' => ['App\Node'];
    }

    #[DataProvider('nonMatchingNameDataProvider')]
    public function testDoesNotResolveNonMatchingName(string $code): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node')
            ->withTypeImportAs('App\Err\Exception', 'Error');

        self::assertSame($code, $this->resolveName($resolver, $code));
    }

    /**
     * Nodes that carry no {@see Name} at all (literal types) pass through the
     * resolver completely untouched — same instance, identical structure.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function literalTypeDataProvider(): iterable
    {
        yield 'int' => ['42'];
        yield 'string' => ['"foo"'];
        yield 'bool true' => ['true'];
        yield 'bool false' => ['false'];
        yield 'null' => ['null'];
    }

    #[DataProvider('literalTypeDataProvider')]
    public function testLeavesLiteralTypesUntouched(string $code): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node');

        $source = $this->parse($code);
        $before = $this->print($source);

        $result = $resolver->resolve($source);

        self::assertSame($source, $result);
        self::assertSame($before, $this->print($result));
    }

    /**
     * A type that references nothing the resolver knows about is returned
     * byte-for-byte identical, regardless of how deeply it is nested.
     */
    public function testLeavesUnrelatedCompoundTypeUntouched(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('App\Node');

        $source = $this->parse('array<int, non-empty-string>|callable(bool): void');
        $before = $this->print($source);

        self::assertSame($before, $this->print($resolver->resolve($source)));
    }

    /**
     * An empty resolver has no rules, so it may not alter any name.
     */
    public function testEmptyResolverIsANoOp(): void
    {
        $source = $this->parse('array{Node, Error\Sub}');
        $before = $this->print($source);

        self::assertSame($before, $this->print(new TypeResolver()->resolve($source)));
    }

    /**
     * An import must resolve to at least one name segment; a value consisting
     * only of namespace separators is malformed and has to blow up rather than
     * silently produce a broken, segment-less name.
     */
    public function testMalformedImportIsRejected(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('\\');

        $this->expectException(\Throwable::class);

        $resolver->resolve($this->parse('Node'));
    }

    /**
     * A leading `\` denotes a fully-qualified (absolute) name. In PHP such a
     * name is resolved from the global namespace and is never affected by any
     * `use` statement, even when its first segment textually equals an alias.
     *
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function fullyQualifiedNameDataProvider(): iterable
    {
        // Collides with `use TypeLang\Parser\Node` — but must stay absolute.
        yield 'fq alias root' => ['\Node', '\Node'];
        yield 'fq alias with tail' => ['\Node\Foo', '\Node\Foo'];
        // Collides with `use TypeLang\Parser\Exception as Error`.
        yield 'fq aliased root' => ['\Error\SemanticException', '\Error\SemanticException'];
        // No collision — trivially unchanged (sanity contrast).
        yield 'fq unrelated' => ['\Some\Other\Node', '\Some\Other\Node'];
    }

    #[DataProvider('fullyQualifiedNameDataProvider')]
    public function testFullyQualifiedNamesIgnoreImports(string $code, string $expected): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node')
            ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');

        self::assertSame($expected, $this->resolveName($resolver, $code));
    }

    /**
     * Built-in scalar/compound types (`int`, `string`, ...) are reserved words.
     * PHP rejects `use Something as int;` outright, so within a type expression
     * such a name always denotes the built-in and can never be rewritten into a
     * class name by an import.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function reservedBuiltinTypeDataProvider(): iterable
    {
        yield 'int' => ['int'];
        yield 'string' => ['string'];
        yield 'bool' => ['bool'];
        yield 'float' => ['float'];
        yield 'array' => ['array'];
        yield 'object' => ['object'];
        yield 'iterable' => ['iterable'];
        yield 'callable' => ['callable'];
        yield 'mixed' => ['mixed'];
        yield 'void' => ['void'];
        yield 'never' => ['never'];
    }

    #[DataProvider('reservedBuiltinTypeDataProvider')]
    public function testReservedBuiltinTypeIsNeverRewrittenByImport(string $reserved): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport("Vendor\\{$reserved}");

        self::assertSame($reserved, $this->resolveName($resolver, $reserved));
    }

    /**
     * `self`, `static` and `parent` are reserved special class references and,
     * like the built-in scalars, cannot be aliased away by a `use` statement.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function reservedSpecialTypeDataProvider(): iterable
    {
        yield 'self' => ['self'];
        yield 'static' => ['static'];
        yield 'parent' => ['parent'];
    }

    #[DataProvider('reservedSpecialTypeDataProvider')]
    public function testReservedSpecialTypeIsNeverRewrittenByImport(string $reserved): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport("Vendor\\{$reserved}");

        self::assertSame($reserved, $this->resolveName($resolver, $reserved));
    }

    /**
     * An import aliases a *whole* leading segment, never a textual prefix of it.
     * `use A\Node;` must not touch `NodeList` or `Node_`, which are distinct
     * identifiers that merely start with the same characters.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function nonMatchingSegmentDataProvider(): iterable
    {
        yield 'longer identifier' => ['NodeList'];
        yield 'trailing underscore' => ['Node_'];
        yield 'first segment differs' => ['X\Node'];
        yield 'alias only in tail' => ['Vendor\Node\Leaf'];
    }

    #[DataProvider('nonMatchingSegmentDataProvider')]
    public function testImportMatchesWholeSegmentOnly(string $code): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('A\Node');

        self::assertSame($code, $this->resolveName($resolver, $code));
    }

    /**
     * Resolving is idempotent: once a short name has been expanded to its
     * fully-qualified form, re-running the resolver leaves it untouched (its
     * first segment no longer matches the alias).
     */
    public function testResolutionIsIdempotent(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImport('TypeLang\Parser\Node');

        $node = $this->parse('Node\Foo');

        $resolver->resolve($node);
        $resolver->resolve($node);

        self::assertInstanceOf(NamedTypeNode::class, $node);
        self::assertSame('TypeLang\Parser\Node\Foo', $node->name->toString());
    }

    /**
     * All trailing segments of an aliased reference are preserved when the
     * alias prefix is substituted.
     */
    public function testAliasSubstitutionKeepsAllTrailingSegments(): void
    {
        $resolver = new TypeResolver()
            ->withTypeImportAs('A\B\Exception', 'Error');

        self::assertSame(
            'A\B\Exception\Sub\Deep',
            $this->resolveName($resolver, 'Error\Sub\Deep'),
        );
    }
}
