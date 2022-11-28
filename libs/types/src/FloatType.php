<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<int|float, float>
 */
final class FloatType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): float
    {
        if (\is_int($value)) {
            return (float)$value;
        }

        if (!\is_float($value)) {
            throw ParseException::fromInvalidType('float', $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): float
    {
        if (\is_int($value)) {
            return (float)$value;
        }

        if (!\is_float($value)) {
            throw SerializeException::fromInvalidType('float', $value);
        }

        return $value;
    }
}
