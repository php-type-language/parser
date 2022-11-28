<?php

declare(strict_types=1);

namespace Hyper\Pool\Instantiator;

use Hyper\Pool\Instantiator\InstantiatorInterface;

/**
 * @template TEntry of object
 * @template-implements InstantiatorInterface<TEntry>
 */
final class ClosureInstantiator implements InstantiatorInterface
{
    /**
     * @param \Closure(TEntry|null):TEntry $instantiator
     */
    public function __construct(
        private readonly \Closure $instantiator,
    ) {
    }

    /**
     * @param \Closure(TEntry|null):TEntry $create
     * @return self
     */
    public static function new(\Closure $creation): self
    {
        return new self($creation);
    }

    /**
     * {@inheritDoc}
     */
    public function create(?object $previous): object
    {
        return ($this->instantiator)($previous);
    }
}
