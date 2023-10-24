<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type;

class TypesListNode extends TypeStatement
{
    public function __construct(
        public readonly TypeStatement $type,
    ) {}
}
