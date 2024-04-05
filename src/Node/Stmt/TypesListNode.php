<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement
 * @template-extends GenericTypeStmt<T>
 */
class TypesListNode extends GenericTypeStmt
{
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'kind' => TypeKind::LIST_KIND,
        ];
    }
}
