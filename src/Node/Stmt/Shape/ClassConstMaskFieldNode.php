<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ClassConstNode;
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

    public function getKey(): string
    {
        $result = $this->key->class->toString()
            . '::' . $this->key->constant?->toString();

        if ($this->key instanceof ClassConstNode) {
            return $result;
        }

        return $result . '*';
    }
}
