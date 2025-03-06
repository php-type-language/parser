<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends GenericTypeNode<T>
 */
class TypeOffsetAccessNode extends GenericTypeNode
{
    public function __construct(
        TypeStatement $type,
        public readonly TypeStatement $access,
    ) {
        parent::__construct($type);
    }
}
