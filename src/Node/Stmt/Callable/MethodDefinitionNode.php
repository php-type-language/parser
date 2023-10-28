<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

final class MethodDefinitionNode extends FunctionDefinitionNode
{
    public function __construct(
        public readonly Visibility $visibility,
        Identifier $name,
        ?ArgumentsListNode $arguments = null,
        ?NamedTypeNode $type = null,
    ) {
        parent::__construct($name, $arguments, $type);
    }
}
