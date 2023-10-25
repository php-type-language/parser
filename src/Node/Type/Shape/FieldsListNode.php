<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Node;

/**
 * @template-implements \IteratorAggregate<array-key, FieldNode>
 */
class FieldsListNode extends Node implements \IteratorAggregate, \Countable, \Stringable
{
    /**
     * @param list<FieldNode> $list
     */
    public function __construct(
        public readonly array $list = [],
        public readonly bool $sealed = true,
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

    public function __toString(): string
    {
        return $this->sealed ? 'sealed' : 'unsealed';
    }
}
