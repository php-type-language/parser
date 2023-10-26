<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Definition;

use TypeLang\Parser\Node\Definition\Template\ParametersListNode;
use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Type\TypeStatement;

final class TypeDefinitionNode extends DefinitionStatement
{
    public function __construct(
        public readonly Identifier $name,
        public readonly ?TypeStatement $type = null,
        public readonly ?ParametersListNode $parameters = null,
    ) {}
}
