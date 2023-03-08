<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

/**
 * @template TOut of object
 *
 * @template-implements TypeInstantiatorInterface<TOut>
 */
final class TypeInstantiator implements TypeInstantiatorInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function new(string $type, iterable $args = []): object
    {
        return (new \ReflectionClass($type))
            ->newInstanceArgs($args);
    }
}
