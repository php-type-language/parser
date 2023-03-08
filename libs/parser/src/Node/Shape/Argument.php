<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Shape;

use Hyper\Parser\Node\Node;
use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class Argument extends Node
{
    public function __construct(
        public readonly Statement $value,
        public readonly ?string $name = null,
        public readonly bool $optional = false,
    ) {
    }
}
