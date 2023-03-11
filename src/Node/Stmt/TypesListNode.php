<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

class TypesListNode extends Statement
{
    /**
     * @param Statement $type
     */
    public function __construct(
        public readonly Statement $type,
    ) {
    }
}
