<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class ClassConstMaskFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public ClassConstMaskNode $key,
        TypeStatement $of,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct($of, $optional, $attributes);
    }

    public function getHashString(): string
    {
        $key = $this->key::class . ':'
            . $this->key->class->toString()
            . '::' . $this->key->constant?->toString();

        return \hash('xxh3', $key);
    }
}
