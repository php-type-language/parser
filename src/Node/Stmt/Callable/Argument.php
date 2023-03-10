<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt\Callable;

use Hyper\Parser\Node\Node;
use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
final class Argument extends Node
{
    public function __construct(
        public readonly Statement $type,
        public readonly ?Modifier $modifier = null,
    ) {
    }
}
