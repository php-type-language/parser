<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<int, int>
 */
final class IntType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): int
    {
        if (!\is_int($value)) {
            throw ParseException::fromInvalidType('int', $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): int
    {
        if (!\is_int($value)) {
            throw SerializeException::fromInvalidType('int', $value);
        }

        return $value;
    }
}
