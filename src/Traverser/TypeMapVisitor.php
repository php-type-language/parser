<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;

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
        $result = ($this->transform)($name);

        if ($result instanceof Name) {
            return $result;
        }

        return $name;
    }

    public function enter(Node $node): ?Command
    {
        switch (true) {
            case $node instanceof NamedTypeNode:
            case $node instanceof CallableTypeNode:
            case $node instanceof ConstMaskNode:
                $node->name = $this->map($node->name);

                return null;

            case $node instanceof ClassConstNode:
            case $node instanceof ClassConstMaskNode:
                $node->class = $this->map($node->class);

                return null;

            default:
                return null;
        }
    }
}
