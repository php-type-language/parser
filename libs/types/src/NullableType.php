<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TPHPValue of mixed
 * @template TDatabaseValue of mixed
 * @template TWrappingType of TypeInterface
 *
 * @template-extends GenericType<TPHPValue, TDatabaseValue, TWrappingType>
 */
final class NullableType extends GenericType
{
    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $this->type->serialize($value);
    }

    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $this->type->serialize($value);
    }
}
