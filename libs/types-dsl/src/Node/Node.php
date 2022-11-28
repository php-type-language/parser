<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node;

use Phplrt\Contracts\Ast\NodeInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
abstract class Node implements NodeInterface
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public readonly int $offset,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
