<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

abstract class BinaryStmt extends Statement
{
    public function __construct(
        public readonly Statement $a,
        public readonly Statement $b,
    ) {
    }
}
