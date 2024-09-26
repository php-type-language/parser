<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * @deprecated Since 1.1, please use {@see TemplateArgumentNode} instead.
 */
class ArgumentNode extends Node
{
    public ?Identifier $hint;

    /**
     * @param Identifier|non-empty-string|null $hint
     */
    public function __construct(
        public TypeStatement $value,
        Identifier|string|null $hint = null,
        public ?AttributeGroupsListNode $attributes = null,
    ) {
        $this->hint = \is_string($hint) ? new Identifier($hint) : $hint;
    }
}
