<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Attribute;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Statement;

final class AttributeNode extends Statement
{
    public function __construct(
        public Name $name,
        public ?AttributeArgumentsListNode $arguments = null,
    ) {}
}
