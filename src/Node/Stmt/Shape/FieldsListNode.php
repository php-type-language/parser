<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @template-implements \IteratorAggregate<array-key, FieldNodeInterface>
 */
class FieldsListNode extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @param list<FieldNodeInterface> $list
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
}
