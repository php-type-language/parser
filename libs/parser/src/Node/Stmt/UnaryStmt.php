<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

abstract class UnaryStmt extends Statement
{
    public function __construct(
        public readonly Statement $type,
    ) {
    }
}
