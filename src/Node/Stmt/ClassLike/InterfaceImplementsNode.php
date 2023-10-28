<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\ClassLike;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

/**
 * @template-implements \IteratorAggregate<array-key, NamedTypeNode>
 */
class InterfaceImplementsNode extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @param array<NamedTypeNode> $list
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
