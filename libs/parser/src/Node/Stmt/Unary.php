<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Stmt\Statement;

abstract class Unary extends Statement
{
    public function __construct(
        public readonly Statement $type,
    ) {
    }
}
