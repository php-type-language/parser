<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<scalar, bool>
 */
final class BoolType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            throw ParseException::fromInvalidType('bool', $value);
        }

        return (bool)$value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): bool
    {
        if (!\is_scalar($value)) {
            throw SerializeException::fromInvalidType('bool', $value);
        }

        return $value;
    }
}
