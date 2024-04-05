<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement
 * @template-extends LogicalTypeNode<T>
 */
class IntersectionTypeNode extends LogicalTypeNode
{
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'kind' => TypeKind::LOGICAL_INTERSECTION_KIND,
        ];
    }
}
