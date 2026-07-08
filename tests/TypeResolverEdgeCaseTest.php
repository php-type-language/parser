<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderClosure;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\TypeResolver;
use TypeLang\Type\NamedTypeNode;

/**
 * Edge-case specification for {@see TypeResolver}.
 *
 * These tests describe how the resolver is *expected* to behave if it mirrored
 * the semantics of PHP `use` statements — not how the current implementation
 * happens to behave. A failing test here therefore points at a place where the
 * resolver diverges from the language it emulates.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TypeResolverEdgeCaseTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    private function resolveName(TypeResolver $resolver, string $code): string
    {
        $node = $resolver->resolve($this->parse($code));

        self::assertInstanceOf(NamedTypeNode::class, $node);

        return $node->name->toString();
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
