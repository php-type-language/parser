<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Node;

/**
 * @template-implements \IteratorAggregate<array-key, ArgumentNodeInterface>
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ArgumentsListNode extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @param list<ArgumentNodeInterface> $list
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
