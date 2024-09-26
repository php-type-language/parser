<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * TODO Add name non-emptiness assertion
 */
final class StringNamedFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public StringLiteralNode $key,
        TypeStatement $of,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct($of, $optional, $attributes);
    }

    public function getKey(): string
    {
        return $this->key->getValue();
    }
}
