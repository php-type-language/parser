<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<int|numeric-string, int|numeric-string>
 */
final class BigIntType implements TypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): int|string
    {
        if (!\is_numeric($value)) {
            throw ParseException::fromInvalidType('int|numeric-string', $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): int|string
    {
        if (!\is_numeric($value)) {
            throw SerializeException::fromInvalidType('int|numeric-string', $value);
        }

        return $value;
    }
}
