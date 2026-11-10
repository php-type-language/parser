<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\ConstTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Node;

/**
 * Replaces the names of types with the ones the given closure returns.
 */
final class TypeMapVisitor extends Visitor
{
    /**
     * @param \Closure(Name):(Name|null) $transform
     */
    public function __construct(
        private readonly \Closure $transform,
    ) {}

    private function map(Name $name): Name
    {
        return ($this->transform)($name) ?? $name;
    }

    /**
     * A namespace of a mask is mapped as the name of its last word, a mask
     * written with none, from the root, or with no words is left as it is.
     *
     * @return ($namespace is null ? null : NamespaceName)
     */
    private function mapNamespace(?NamespaceName $namespace): ?NamespaceName
    {
        if ($namespace === null || $namespace->kind !== NamespaceKind::Qualified) {
            return $namespace;
        }

        $parts = $namespace->toArray();
        $namePart = \array_pop($parts);

        if ($namePart === null) {
            return $namespace;
        }

        $mapped = $this->map(new Name(
            value: $namePart,
            namespace: $this->getMappedNamespace($parts),
            offset: $namespace->offset,
        ));

        return new NamespaceName(
            value: \implode(NamespaceName::DELIMITER, $mapped->toArray()),
            kind: $mapped->namespace->kind ?? NamespaceKind::Qualified,
            offset: $namespace->offset,
        );
    }

    /**
     * @param list<non-empty-string> $parts
     */
    private function getMappedNamespace(array $parts): ?NamespaceName
    {
        if (\count($parts) === 0) {
            return null;
        }

        return new NamespaceName(\implode(NamespaceName::DELIMITER, $parts));
    }

    /**
     * @return ($owner is Name ? Name : ($owner is null ? null : NamespaceName))
     */
    private function mapOwner(Name|NamespaceName|null $owner): Name|NamespaceName|null
    {
        if ($owner instanceof Name) {
            return $this->map($owner);
        }

        return $this->mapNamespace($owner);
    }

    public function enter(Node $node): ?Command
    {
        switch (true) {
            case $node instanceof NamedTypeNode:
            case $node instanceof CallableTypeNode:
                $node->name = $this->map($node->name);

                return null;

            case $node instanceof ConstTypeNode:
                $node->owner = $this->mapOwner($node->owner);

                return null;

            case $node instanceof ConstMaskTypeNode:
                $node->owner = $this->mapOwner($node->owner);

                return null;

            default:
                return null;
        }
    }
}
