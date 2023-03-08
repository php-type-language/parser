<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Name;
use Hyper\Parser\Node\Template\Parameters;
use Hyper\Parser\Node\Shape\Shape;
use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class NamedType extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?Parameters $parameters = null,
        public readonly ?Shape $arguments = null,
    ) {
    }
}
