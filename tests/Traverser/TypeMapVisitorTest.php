<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\MaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\UnionTypeNode;
use TypeLang\Type\WildcardNode;

final class TypeMapVisitorTest extends TestCase
{
    private function alias(string $to = 'Aliased'): TypeMapVisitor
    {
        return new TypeMapVisitor(static fn(Name $name): Name => Name::createFromString($to));
    }

    #[Test]
    public function theNameOfANamedTypeIsTransformed(): void
    {
        $node = new NamedTypeNode(Name::createFromString('Example'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->toString());
    }

    #[Test]
    public function theNameOfACallableTypeIsTransformed(): void
    {
        $node = new CallableTypeNode(Name::createFromString('callable'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->toString());
    }

    #[Test]
    public function theNamespaceOfAConstMaskIsTransformed(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Identifier('SOME_'), new WildcardNode()]),
            Name::createFromString('Vendor'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertInstanceOf(Name::class, $node->namespaceOrFullyQualified);
        self::assertSame('Aliased', $node->namespaceOrFullyQualified->toString());
    }

    #[Test]
    public function aConstMaskWithoutANamespaceIsLeftAlone(): void
    {
        $node = new ConstMaskNode(new MaskNode([new WildcardNode(), new Identifier('_SOME')]));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertFalse($node->namespaceOrFullyQualified);
    }

    #[Test]
    public function theClassOfAClassConstIsTransformed(): void
    {
        $node = new ClassConstNode(
            class: Name::createFromString('Example'),
            constant: new Identifier('CONSTANT'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theConstantOfAClassConstIsNotTransformed(): void
    {
        $node = new ClassConstNode(
            class: Name::createFromString('Example'),
            constant: new Identifier('CONSTANT'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('CONSTANT', $node->constant->toString());
    }

    #[Test]
    public function theClassOfAClassConstMaskIsTransformed(): void
    {
        $node = new ClassConstMaskNode(Name::createFromString('Example'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theNullResultKeepsTheOriginalName(): void
    {
        $expected = Name::createFromString('Example');
        $node = new NamedTypeNode($expected);

        Traverser::new([new TypeMapVisitor(static fn(Name $name): ?Name => null)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }

    #[Test]
    public function theTransformationIsAppliedToEveryNestedType(): void
    {
        $node = new UnionTypeNode([
            new NamedTypeNode(Name::createFromString('A')),
            new NamedTypeNode(Name::createFromString('B'))
        ]);

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame(
            ['Aliased', 'Aliased'],
            \array_map(
                static fn(NamedTypeNode $type): string => $type->name->toString(),
                $node->statements,
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

        Traverser::new([$visitor])->traverse([
            new NamedTypeNode(Name::createFromString('Foo\\Bar')),
        ]);

        self::assertSame(['Foo\\Bar'], $received);
    }

    #[Test]
    public function theTransformationIsNotAppliedToAnArbitraryName(): void
    {
        $received = [];

        $visitor = new TypeMapVisitor(static function (Name $name) use (&$received): Name {
            $received[] = $name->toString();

            return $name;
        });

        Traverser::new([$visitor])->traverse([Name::createFromString('Example')]);

        self::assertSame([], $received);
    }

    #[Test]
    public function theTransformedNameIsVisitedInsteadOfTheOriginalOne(): void
    {
        $node = new NamedTypeNode(Name::createFromString('Example'));
        $expected = Name::createFromString('Aliased');

        Traverser::new([new TypeMapVisitor(static fn(Name $name): Name => $expected)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }
}
