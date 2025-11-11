<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class ConstMaskFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public ConstMaskNode $key,
        TypeStatement $of,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct($of, $optional, $attributes);
    }

    public function getHashString(): string
    {
        $key = $this->key::class . ':'
            . $this->key->name->toString();

        return \hash('xxh3', $key);
    }
}
