<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Attribute;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

class AttributeArgumentNode extends Node
{
    public function __construct(
        public TypeStatement $value,
        public ?AttributeGroupsListNode $attributes = null,
    ) {}
}
