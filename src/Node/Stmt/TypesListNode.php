<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

class TypesListNode extends TypeStatement
{
    public function __construct(
        public TypeStatement $type,
    ) {}
}
