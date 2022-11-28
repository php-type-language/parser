<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Stmt;

use Hyper\Type\DSL\Node\Argument;
use Hyper\Type\DSL\Node\Name;
use Hyper\Type\DSL\Node\NamedArgument;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class TypeStmt extends Statement
{
    /**
     * @var array<Argument|NamedArgument>
     */
    public readonly array $args;

    /**
     * @param int<0, max> $offset
     * @param Name $name
     * @param iterable<Argument|NamedArgument> $args
     */
    public function __construct(
        int $offset,
        public readonly Name $name,
        iterable $args = [],
    ) {
        parent::__construct($offset);

        $this->args = [...$args];
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        yield 'name' => $this->name;
        yield 'args' => $this->args;
    }
}
