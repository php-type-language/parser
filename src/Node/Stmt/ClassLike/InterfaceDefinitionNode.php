<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\ClassLike;

use TypeLang\Parser\Node\Stmt\Callable\MethodsListNode;
use TypeLang\Parser\Node\Stmt\Template\ParametersListNode;
use TypeLang\Parser\Node\Identifier;

final class InterfaceDefinitionNode extends ClassLikeDefinition
{
    public function __construct(
        Identifier $name,
        ?ParametersListNode $parameters = null,
        ?MethodsListNode $methods = null,
        public readonly ?InterfaceImplementsNode $implements = null,
    ) {
        parent::__construct($name, $parameters, $methods);
    }
}
