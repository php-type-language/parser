<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\TypeNode;

final class TypeMapVisitorTest extends TestCase
{
    private function alias(string $to = 'Aliased'): TypeMapVisitor
    {
        return new TypeMapVisitor(static fn(Name $name): Name => new Name($to));
    }

    /**
     * @template T of TypeNode
     *
     * @param class-string<T> $class
     * @return T
     * @throws \Throwable
     */
    private function parseAs(string $class, string $code): TypeNode
    {
        $node = $this->parse($code);

        self::assertInstanceOf($class, $node);

        return $node;
    }

    #[Test]
    public function theNameOfANamedTypeIsTransformed(): void
    {
        $node = $this->parseAs(NamedTypeNode::class, 'Example');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->toString());
    }

    #[Test]
    public function theNameOfACallableTypeIsTransformed(): void
    {
        $node = $this->parseAs(CallableTypeNode::class, 'callable()');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->toString());
    }

    #[Test]
    public function theNamespaceOfAConstMaskIsTransformed(): void
    {
        $node = $this->parseAs(ConstMaskTypeNode::class, 'Vendor\SOME_*');

        Traverser::new([$this->alias('Aliased')])->traverse([$node]);

        self::assertInstanceOf(NamespaceName::class, $node->namespace);
        self::assertSame('Aliased', $node->namespace->toString());
    }

    #[Test]
    public function aConstMaskWithoutANamespaceIsLeftAlone(): void
    {
        $node = $this->parseAs(ConstMaskTypeNode::class, '*_SOME');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertNull($node->namespace);
    }

    #[Test]
    public function aFullyQualifiedConstMaskIsLeftAlone(): void
    {
        $node = $this->parseAs(ConstMaskTypeNode::class, '\Vendor\SOME_*');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertNotNull($node->namespace);
        self::assertSame('\Vendor', $node->namespace->toString());
    }

    #[Test]
    public function theClassOfAClassConstIsTransformed(): void
    {
        $node = $this->parseAs(ClassConstTypeNode::class, 'Example::CONSTANT');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theConstantOfAClassConstIsNotTransformed(): void
    {
        $node = $this->parseAs(ClassConstTypeNode::class, 'Example::CONSTANT');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('CONSTANT', $node->constant->toString());
    }

    #[Test]
    public function theClassOfAClassConstMaskIsTransformed(): void
    {
        $node = $this->parseAs(ClassConstMaskTypeNode::class, 'Example::*');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theNullResultKeepsTheOriginalName(): void
    {
        $node = $this->parseAs(NamedTypeNode::class, 'Example');
        $expected = $node->name;

        Traverser::new([new TypeMapVisitor(static fn(Name $name): ?Name => null)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }

    #[Test]
    public function theTransformationIsAppliedToEveryNestedType(): void
    {
        $node = $this->parseAs(UnionTypeNode::class, 'A|B');

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame(
            ['Aliased', 'Aliased'],
            \array_map(
                static fn(NamedTypeNode $type): string => $type->name->toString(),
                $node->types,
            ),
        );
    }

    #[Test]
    public function theTransformationReceivesTheOriginalName(): void
    {
        $received = [];

        $visitor = new TypeMapVisitor(static function (Name $name) use (&$received): Name {
            $received[] = $name->toString();

            return $name;
        });

        Traverser::new([$visitor])->traverse([$this->parse('Foo\Bar')]);

        self::assertSame(['Foo\Bar'], $received);
    }

    #[Test]
    public function theTransformationIsNotAppliedToAnArbitraryName(): void
    {
        $received = [];

        $visitor = new TypeMapVisitor(static function (Name $name) use (&$received): Name {
            $received[] = $name->toString();

            return $name;
        });

        Traverser::new([$visitor])->traverse([new Name('Example')]);

        self::assertSame([], $received);
    }

    #[Test]
    public function theTransformedNameIsVisitedInsteadOfTheOriginalOne(): void
    {
        $node = $this->parseAs(NamedTypeNode::class, 'Example');
        $expected = new Name('Aliased');

        Traverser::new([new TypeMapVisitor(static fn(Name $name): Name => $expected)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }
}
