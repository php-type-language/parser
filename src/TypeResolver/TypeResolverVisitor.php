<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\Visitor;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
final class TypeResolverVisitor extends Visitor
{
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
                $node->name = $mapped;
            }
        }

        return null;
    }
}
