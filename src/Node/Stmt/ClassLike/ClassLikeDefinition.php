<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\ClassLike;

use TypeLang\Parser\Node\Stmt\DefinitionStatement;
use TypeLang\Parser\Node\Stmt\Callable\MethodsListNode;
use TypeLang\Parser\Node\Stmt\Template\ParametersListNode;
use TypeLang\Parser\Node\Identifier;

abstract class ClassLikeDefinition extends DefinitionStatement
{
    public function __construct(
        public readonly Identifier $name,
        public readonly ?ParametersListNode $parameters = null,
        public readonly ?MethodsListNode $methods = null,
    ) {}
}
