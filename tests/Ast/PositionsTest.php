<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * A type stands in a place of another one as a whole: the place keeps the
 * node of it as it is, whatever kind of a type it is.
 */
#[Group('unit'), Group('type-lang/parser')]
final class PositionsTest extends AstTestCase
{
    private const MARK = '%s';

    /**
     * The types of the kinds that have a node of their own, the node class
     * is the one the place must hold.
     *
     * @return array<non-empty-string, array{non-empty-string, class-string<TypeNode>}>
     */
    private static function types(): array
    {
        return [
            'condition' => ['T is int ? A : B', ConditionalTypeNode::class],
            'union' => ['A|B', UnionTypeNode::class],
            'intersection' => ['A&B', IntersectionTypeNode::class],
            'nullable' => ['?A', NullableTypeNode::class],
            'list' => ['A[]', TypesListTypeNode::class],
            'offset access' => ['A[K]', TypeOffsetAccessTypeNode::class],
            'callable' => ['callable(int): void', CallableTypeNode::class],
            'named' => ['Foo<A>', NamedTypeNode::class],
        ];
    }

    /**
     * The places a whole type stands in: the source with a mark where the
     * type goes, and what reads the node out of the tree.
     *
     * @return array<non-empty-string, array{non-empty-string, \Closure(mixed): mixed}>
     */
    private static function places(): array
    {
        return [
            'template argument' => ['Foo<%s>', static fn(NamedTypeNode $n) => $n->arguments?->items[0]->value],
            'second template argument' => ['Foo<int, %s>', static fn(NamedTypeNode $n) => $n->arguments?->items[1]->value],
            'shape value' => ['array{a: %s}', static fn(NamedTypeNode $n) => $n->fields?->items[0]->type],
            'implicit shape value' => ['array{%s}', static fn(NamedTypeNode $n) => $n->fields?->items[0]->type],
            'parameter' => ['callable(%s $a)', static fn(CallableTypeNode $n) => $n->parameters[0]->type],
            'variadic parameter' => ['callable(...%s)', static fn(CallableTypeNode $n) => $n->parameters[0]->type],
            'offset key' => ['T[%s]', static fn(TypeOffsetAccessTypeNode $n) => $n->access],
            'upper bound' => ['callable<T of %s>(): void', static fn(CallableTypeNode $n) => $n->templates?->items[0]->upper?->type],
            'lower bound' => ['callable<T super %s>(): void', static fn(CallableTypeNode $n) => $n->templates?->items[0]->lower?->type],
            'template default' => ['callable<T = %s>(): void', static fn(CallableTypeNode $n) => $n->templates?->items[0]->default],
            'then branch' => ['A is B ? %s : C', static fn(ConditionalTypeNode $n) => $n->then],
            'else branch' => ['A is B ? C : %s', static fn(ConditionalTypeNode $n) => $n->else],
        ];
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, \Closure(mixed): mixed, class-string<TypeNode>}>
     */
    public static function placeDataProvider(): iterable
    {
        foreach (self::places() as $place => [$template, $read]) {
            foreach (self::types() as $kind => [$type, $class]) {
                yield $kind . ' as a ' . $place => [\str_replace(self::MARK, $type, $template), $read, $class];
            }
        }
    }

    /**
     * @param \Closure(mixed): mixed $read
     * @param class-string<TypeNode> $class
     * @throws \Throwable
     */
    #[DataProvider('placeDataProvider')]
    public function testPlaceHoldsTheTypeAsItIs(string $source, \Closure $read, string $class): void
    {
        $held = $read($this->parse($source));

        self::assertInstanceOf($class, $held, 'The place of ' . $source . ' holds ' . \get_debug_type($held));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, \Closure(mixed): mixed, class-string<TypeNode>}>
     */
    public static function groupedPlaceDataProvider(): iterable
    {
        $places = [
            'union member' => ['A|%s', static fn(UnionTypeNode $n) => $n->types[1]],
            'intersection member' => ['A&%s', static fn(IntersectionTypeNode $n) => $n->types[1]],
            'nullable' => ['?%s', static fn(NullableTypeNode $n) => $n->type],
            'list' => ['%s[]', static fn(TypesListTypeNode $n) => $n->type],
            'offset access' => ['%s[K]', static fn(TypeOffsetAccessTypeNode $n) => $n->type],
            'return type' => ['callable(): %s', static fn(CallableTypeNode $n) => $n->type],
            'subject of a condition' => ['%s is int ? A : B', static fn(ConditionalTypeNode $n) => $n->condition->subject],
            'target of a condition' => ['A is %s ? B : C', static fn(ConditionalTypeNode $n) => $n->condition->target],
        ];

        foreach ($places as $place => [$template, $read]) {
            foreach (self::types() as $kind => [$type, $class]) {
                // The group of a union in a union is the union itself, the rest keep the group out of the tree
                if ($place === 'union member' && $class === UnionTypeNode::class) {
                    continue;
                }

                if ($place === 'intersection member' && $class === IntersectionTypeNode::class) {
                    continue;
                }

                yield 'grouped ' . $kind . ' as a ' . $place => [
                    \str_replace(self::MARK, '(' . $type . ')', $template),
                    $read,
                    $class,
                ];
            }
        }
    }

    /**
     * A group builds no node, so the place holds the node of the type in it.
     *
     * @param \Closure(mixed): mixed $read
     * @param class-string<TypeNode> $class
     * @throws \Throwable
     */
    #[DataProvider('groupedPlaceDataProvider')]
    public function testGroupHoldsTheTypeInItAsItIs(string $source, \Closure $read, string $class): void
    {
        $held = $read($this->parse($source));

        self::assertInstanceOf($class, $held, 'The place of ' . $source . ' holds ' . \get_debug_type($held));
    }
}
