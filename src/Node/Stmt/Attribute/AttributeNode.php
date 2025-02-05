<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Attribute;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Node;

final class AttributeNode extends Node
{
    public function __construct(
        public Name $name,
        public ?AttributeArgumentsListNode $arguments = null,
    ) {}
}
