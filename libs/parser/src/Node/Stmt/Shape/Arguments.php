<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt\Shape;

use Hyper\Parser\Node\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class Arguments extends Node
{
    /**
     * @param array<Argument> $list
     */
    public function __construct(
        public readonly array $list = [],
        public readonly bool $sealed = true,
    ) {
    }
}
