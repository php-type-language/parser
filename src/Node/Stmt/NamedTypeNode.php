<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Template\TemplateArgumentsListNode;

class NamedTypeNode extends TypeStatement
{
    public Name $name;

    /**
     * @param Name|Identifier|non-empty-string $name
     */
    public function __construct(
        Name|Identifier|string $name,
        public ?TemplateArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {
        $this->name = $name instanceof Name ? $name : new Name($name);
    }
}
