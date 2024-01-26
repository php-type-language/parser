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
        return \reset($this->items) ?: null;
    }

    /**
     * @return TNode|null
     */
    public function last(): ?Node
    {
        return \end($this->items) ?: null;
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

    public function toArray(): array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        return ['items' => $items];
    }
}
