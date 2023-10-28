<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode;

class NamedTypeNode extends TypeStatement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?ArgumentsListNode $arguments = null,
        public readonly ?FieldsListNode $fields = null,
    ) {}
}
