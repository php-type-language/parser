<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Template;

use Hyper\Parser\Node\Node;

class Parameters extends Node
{
    /**
     * @param array<Parameter> $list
     */
    public function __construct(
        public readonly array $list = [],
    ) {
    }
}
