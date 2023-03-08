<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Template;

use Hyper\Parser\Node\Node;
use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class Parameter extends Node
{
    /**
     * @param Statement $value
     */
    public function __construct(
        public readonly Statement $value
    ) {
    }
}
