<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement
 * @template-extends LogicalTypeNode<T>
 */
class UnionTypeNode extends LogicalTypeNode
{
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => TypeKind::LOGICAL_UNION_KIND,
        ];
    }
}
