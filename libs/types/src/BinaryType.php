<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<string, string|resource>
 */
final class BinaryType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): string
    {
        if (!\is_scalar($value)) {
            throw ParseException::fromInvalidType('string', $value);
        }

        return (string)$value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): string
    {
        if ($value instanceof \BackedEnum || $value instanceof \Stringable) {
            return (string)$value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if (!\is_scalar($value)) {
            throw SerializeException::fromInvalidType('string', $value);
        }

        return (string)$value;
    }
}
