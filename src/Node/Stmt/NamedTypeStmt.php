<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Name;
use Hyper\Parser\Node\Stmt\Shape\Arguments;
use Hyper\Parser\Node\Stmt\Template\Parameters;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class NamedTypeStmt extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?Parameters $parameters = null,
        public readonly ?Arguments $arguments = null,
    ) {
    }
}
