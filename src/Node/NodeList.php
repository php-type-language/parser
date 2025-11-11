<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @template TNode of Node = Node
 * @template-implements \IteratorAggregate<array-key, TNode>
 * @template-implements \ArrayAccess<int<0, max>, TNode>
 */
abstract class NodeList extends Node implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable
{
    /**
     * @param list<TNode> $items
     */
    public function __construct(
        public array $items = [],
    ) {}

    /**
     * Returns the ordinal number (position) of an element {@see TNode} in
     * a node list, starting with index 0.
     *
     * Returns {@see null} if the element {@see TNode} does not belong
     * to the node list.
     *
     * @param TNode $node
     *
     * @return int<0, max>|null
     */
    public function findIndex(object $node): ?int
    {
        $index = \array_search($node, $this->items, true);

        if (\is_int($index)) {
            return $index;
        }

        return null;
    }

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

    public function offsetExists(mixed $offset): bool
    {
        // @phpstan-ignore-next-line
        assert(\is_int($offset) && $offset >= 0);

        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ?Node
    {
        // @phpstan-ignore-next-line
        assert(\is_int($offset) && $offset >= 0);

        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // @phpstan-ignore-next-line
        assert(\is_int($offset) && $offset >= 0);
        // @phpstan-ignore-next-line
        assert($value instanceof Node);

        // @phpstan-ignore-next-line
        $this->items[$offset] = $value;

        if (!\array_is_list($this->items)) {
            $this->items = \array_values($this->items);
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        // @phpstan-ignore-next-line
        assert(\is_int($offset) && $offset >= 0);

        $items = $this->items;
        unset($items[$offset]);
        $this->items = \array_values($items);
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
