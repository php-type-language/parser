<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Name;
use Hyper\Parser\Node\Stmt\Callable\Arguments;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class CallableTypeStmt extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly Arguments $arguments,
        public readonly ?Statement $type = null,
    ) {
    }
}
