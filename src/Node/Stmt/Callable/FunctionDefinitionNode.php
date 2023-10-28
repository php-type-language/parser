<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\Stmt\DefinitionStatement;
use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

class FunctionDefinitionNode extends DefinitionStatement
{
    public function __construct(
        public readonly Identifier $name,
        public readonly ?ArgumentsListNode $arguments = null,
        public readonly ?NamedTypeNode $type = null,
    ) {}
}
