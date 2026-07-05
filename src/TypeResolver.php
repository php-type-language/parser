<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Type\TypeNode;

final class TypeResolver implements TypeResolverInterface
{
    public function resolve(TypeNode $type, callable $transform): TypeNode
    {
        Traverser::through(new TypeMapVisitor($transform(...)), [$type]);

        return $type;
    }
}
