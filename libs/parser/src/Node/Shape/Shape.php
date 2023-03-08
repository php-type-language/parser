<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Shape;

use Hyper\Parser\Node\Node;

class Shape extends Node
{
    /**
     * @param array<Argument> $list
     */
    public function __construct(
        public readonly array $list = [],
    ) {
    }
}
