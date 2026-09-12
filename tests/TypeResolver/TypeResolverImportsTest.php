<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithMethodStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\NoImportsStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\SimpleClassStub;
use TypeLang\Parser\TypeResolver;
use TypeLang\Type\NamedTypeNode;

/**
 * Tests for the {@see TypeResolver} imports read out of an existing class
 * or function declaration.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TypeResolverImportsTest extends TypeResolverTestCase
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
     * @throws \ReflectionException
     */
    private function functionStub(): \ReflectionFunction
    {
        require_once __DIR__ . '/Stub/functions.php';

        return new \ReflectionFunction(__NAMESPACE__ . '\\Stub\\exampleFunctionStub');
    }

    #[Test]
    public function theNonAliasedImportOfAClassIsResolved(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromClass(new \ReflectionClass(SimpleClassStub::class));

        self::assertSame('Some\\Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theAliasedImportOfAClassIsResolved(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromClass(new \ReflectionClass(SimpleClassStub::class));

        self::assertSame(
            'Some\\Any\\Test\\Nested',
            $this->resolveName($resolver, 'Example\\Nested'),
        );
    }

    #[Test]
    public function withTypeImportsFromClassReturnsANewInstance(): void
    {
        $resolver = new TypeResolver();

        self::assertNotSame(
            $resolver,
            $resolver->withTypeImportsFromClass(new \ReflectionClass(SimpleClassStub::class)),
        );
    }

    #[Test]
    public function withTypeImportsFromClassDoesNotModifyTheOriginalResolver(): void
    {
        $resolver = new TypeResolver();
        $resolver->withTypeImportsFromClass(new \ReflectionClass(SimpleClassStub::class));

        self::assertSame('Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theImportsOfAClassAreMergedWithTheExistingOnes(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImport('App\\Node')
            ->withTypeImportsFromClass(new \ReflectionClass(SimpleClassStub::class));

        self::assertSame('App\\Node', $this->resolveName($resolver, 'Node'));
        self::assertSame('Some\\Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theClassWithoutImportsChangesNothing(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromClass(new \ReflectionClass(NoImportsStub::class));

        self::assertSame('Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theInternalClassChangesNothing(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromClass(new \ReflectionClass(\stdClass::class));

        self::assertSame('Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theNonAliasedImportOfAFunctionIsResolved(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromFunction($this->functionStub());

        self::assertSame('Some\\Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theAliasedImportOfAFunctionIsResolved(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromFunction($this->functionStub());

        self::assertSame(
            'Some\\Any\\Test\\Nested',
            $this->resolveName($resolver, 'Example\\Nested'),
        );
    }

    #[Test]
    public function withTypeImportsFromFunctionReturnsANewInstance(): void
    {
        $resolver = new TypeResolver();

        self::assertNotSame(
            $resolver,
            $resolver->withTypeImportsFromFunction($this->functionStub()),
        );
    }

    #[Test]
    public function withTypeImportsFromFunctionDoesNotModifyTheOriginalResolver(): void
    {
        $resolver = new TypeResolver();
        $resolver->withTypeImportsFromFunction($this->functionStub());

        self::assertSame('Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theImportsOfAMethodAreReadFromItsDeclaringClass(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromFunction(
                new \ReflectionMethod(ClassWithMethodStub::class, 'example'),
            );

        self::assertSame('Some\\Method\\Any', $this->resolveName($resolver, 'Any'));
        self::assertSame(
            'Some\\Method\\Any\\Test',
            $this->resolveName($resolver, 'Example'),
        );
    }

    #[Test]
    public function theInternalFunctionChangesNothing(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromFunction(new \ReflectionFunction('strlen'));

        self::assertSame('Any', $this->resolveName($resolver, 'Any'));
    }

    #[Test]
    public function theClosureIsReadWithTheImportsOfItsFile(): void
    {
        $resolver = (new TypeResolver())
            ->withTypeImportsFromFunction(
                new \ReflectionFunction(static fn(): int => 42),
            );

        self::assertSame(
            'TypeLang\\Parser\\TypeResolver',
            $this->resolveName($resolver, 'TypeResolver'),
        );
    }
}
