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
     * @param list<TNode> $items
     */
    public function __construct(
        public array $items = [],
    ) {}

    /**
     * @return TNode|null
     */
    public function first(): ?Node
    {
        $first = \reset($this->items);

        return $first instanceof Node ? $first : null;
    }

    /**
     * @return TNode|null
     */
    public function last(): ?Node
    {
        $last = \end($this->items);

        return $last instanceof Node ? $last : null;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->items);
    }
}
