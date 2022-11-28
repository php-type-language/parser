<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-implements TypeInterface<mixed, mixed>
 */
final class AnyType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): mixed
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): mixed
    {
        return $value;
    }
}
