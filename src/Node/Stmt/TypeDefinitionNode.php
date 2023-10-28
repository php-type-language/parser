<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Stmt\Template\ParametersListNode;
use TypeLang\Parser\Node\Identifier;

final class TypeDefinitionNode extends DefinitionStatement
{
    public function __construct(
        public readonly Identifier $name,
        public readonly ?ParametersListNode $parameters = null,
        public readonly ?TypeStatement $type = null,
    ) {}
}
