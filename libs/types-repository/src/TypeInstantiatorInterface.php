<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

/**
 * @template TOut of object
 */
interface TypeInstantiatorInterface
{
    /**
     * @param class-string<TOut> $type
     * @param array|(\ArrayAccess&\Countable&\Traversable) $args
     *
     * @return TOut
     */
    public function new(string $type, iterable $args = []): object;
}
