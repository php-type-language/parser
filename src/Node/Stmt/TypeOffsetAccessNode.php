<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends GenericTypeStmt<T>
 */
class TypeOffsetAccessNode extends GenericTypeStmt
{
    public function __construct(
        TypeStatement $type,
        public readonly TypeStatement $access,
    ) {
        parent::__construct($type);
    }
}
