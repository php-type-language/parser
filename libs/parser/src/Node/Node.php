<?php

declare(strict_types=1);

namespace Hyper\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
abstract class Node
{
    /**
     * @var int<0, max>
     */
    public int $offset = 0;
}
