<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\TypeResolver;
use TypeLang\Printer\PrettyTypePrinter;

/**
 * Tests for {@see TypeResolver} resolving names by the namespace and by the
 * classes of `self`, `static` and `parent`.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TypeResolverScopeTest extends TypeResolverTestCase
{
    /**
     * @return iterable<non-empty-string, array{
     *     TypeResolver,
     *     non-empty-string,
     *     non-empty-string,
     * }>
     */
    public static function namesDataProvider(): iterable
    {
        $app = (new TypeResolver())->withNamespace('App', static fn(string $name): bool => true);
        $existing = (new TypeResolver())->withNamespace('TypeLang\Parser');

        yield 'relative name' => [$existing, 'list<TypeParser>', 'list<TypeLang\Parser\TypeParser>'];
        yield 'qualified name' => [$app, 'Model\Address', 'App\Model\Address'];
        yield 'relative to namespace' => [$app, 'namespace\Address', 'App\Address'];
        yield 'fully qualified name' => [$app, '\Address', '\Address'];
        yield 'builtin type' => [$app, 'int', 'int'];
        yield 'import over namespace' => [$app->withTypeImport('Other\Address'), 'Address', 'Other\Address'];
        yield 'namespace kept by import' => [
            (new TypeResolver())->withTypeImport('Other\Address')->withNamespace('App'),
            'Address',
            'Other\Address',
        ];
        yield 'existing class of namespace' => [$existing, 'TypeParser', 'TypeLang\Parser\TypeParser'];
        yield 'name that is not a class' => [$existing, 'list<uuid>', 'list<uuid>'];
        yield 'name of every class' => [$app, 'Address', 'App\Address'];
        yield 'global namespace' => [(new TypeResolver())->withNamespace(''), 'Address', 'Address'];
        yield 'relative to global namespace' => [
            (new TypeResolver())->withNamespace(''),
            'namespace\Address',
            'Address',
        ];
        yield 'no namespace' => [new TypeResolver(), 'namespace\Address', 'namespace\Address'];
    }

    /**
     * @param non-empty-string $code
     * @param non-empty-string $expected
     */
    #[DataProvider('namesDataProvider')]
    public function testResolvesNameByNamespace(TypeResolver $resolver, string $code, string $expected): void
    {
        self::assertSame($expected, $this->resolveAndPrint($resolver, $code));
    }

    /**
     * @return iterable<non-empty-string, array{
     *     non-empty-string,
     *     non-empty-string,
     * }>
     */
    public static function classesDataProvider(): iterable
    {
        yield 'self' => ['self', 'ArrayIterator'];
        yield 'static' => ['static', 'RecursiveArrayIterator'];
        yield 'owner of constant' => ['self::CHILD_*', 'ArrayIterator::CHILD_*'];
        yield 'self in namespace' => ['list<self>', 'list<ArrayIterator>'];
    }

    /**
     * @param non-empty-string $code
     * @param non-empty-string $expected
     */
    #[DataProvider('classesDataProvider')]
    public function testResolvesSpecialNameByClasses(string $code, string $expected): void
    {
        $resolver = (new TypeResolver())
            ->withNamespace('App')
            ->withClasses(\ArrayIterator::class, \RecursiveArrayIterator::class);

        self::assertSame($expected, $this->resolveAndPrint($resolver, $code));
    }

    public function testParentIsParentOfSelf(): void
    {
        $resolver = (new TypeResolver())->withClasses(\RecursiveArrayIterator::class);

        self::assertSame('ArrayIterator', $this->resolveAndPrint($resolver, 'parent'));
        self::assertSame('RecursiveArrayIterator', $this->resolveAndPrint($resolver, 'static'));
    }

    public function testParentOfClassWithoutParentIsRejected(): void
    {
        $resolver = (new TypeResolver())->withClasses(\ArrayIterator::class);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('class "ArrayIterator" has no parent, "parent" cannot be resolved');

        $resolver->resolve($this->parse('parent'));
    }

    public function testSpecialNameIsKeptWithoutClasses(): void
    {
        self::assertSame('self', $this->resolveAndPrint((new TypeResolver())->withNamespace('App'), 'self'));
    }

    public function testImportsAddedLaterKeepNamespaceAndClasses(): void
    {
        $resolver = (new TypeResolver())
            ->withNamespace('TypeLang\Parser')
            ->withClasses(\ArrayIterator::class)
            ->withTypeImport('Other\Status');

        self::assertSame(
            'Other\Status|TypeLang\Parser\TypeParser|ArrayIterator',
            $this->resolveAndPrint($resolver, 'Status|TypeParser|self'),
        );
    }

    /**
     * @param non-empty-string $code
     */
    private function resolveAndPrint(TypeResolver $resolver, string $code): string
    {
        return (new PrettyTypePrinter())->print($resolver->resolve($this->parse($code)));
    }
}
