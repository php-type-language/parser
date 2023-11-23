<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @template TNode of Node
 *
 * @template-implements \IteratorAggregate<array-key, TNode>
 */
abstract class NodeList extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @param list<TNode> $list
     */
    public function __construct(
        public array $list = [],
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
