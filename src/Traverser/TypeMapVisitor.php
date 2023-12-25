<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

final class TypeMapVisitor extends Visitor
{
    /**
     * @param \Closure(Name):(Name|null) $transform
     */
    public function __construct(
        private readonly \Closure $transform,
    ) {}

    private function containsName(Node $node): bool
    {
        return $node instanceof NamedTypeNode
            || $node instanceof CallableTypeNode
            || $node instanceof ConstMaskNode
            || $node instanceof ClassConstMaskNode
        ;
    }

    public function enter(Node $node): ?Command
    {
        if ($this->containsName($node)) {
            /**
             * @var object{name: Name} $node
             * @psalm-suppress MixedAssignment
             */
            $mapped = ($this->transform)($node->name);

            if ($mapped instanceof Name) {
                $node->key = $mapped;
            }
        }

        return null;
    }
}
