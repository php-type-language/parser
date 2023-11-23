<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Stmt\Condition\Condition;

final class TernaryConditionNode extends TypeStatement
{
    public function __construct(
        public Condition $condition,
        public TypeStatement $then,
        public TypeStatement $else,
    ) {}
}
