<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class NumericFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public IntLiteralNode $key,
        TypeStatement $of,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct($of, $optional, $attributes);
    }

    public function getKey(): int
    {
        return $this->key->getValue();
    }
}
