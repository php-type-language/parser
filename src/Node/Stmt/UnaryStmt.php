<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
abstract class UnaryStmt extends Statement
{
    public function __construct(
        public readonly Statement $type,
    ) {
    }
}
