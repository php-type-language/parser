<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Definition\Template;

use TypeLang\Parser\Node\Node;

/**
 * @template-implements \IteratorAggregate<array-key, ParameterNode>
 */
class ParametersListNode extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @param array<ParameterNode> $list
     */
    public function __construct(
        public readonly array $list = [],
    ) {}

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->list);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->list);
    }
}
