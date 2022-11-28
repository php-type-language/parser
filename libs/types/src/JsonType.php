<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;

/**
 * @template-implements TypeInterface<scalar|object, string|resource>
 */
final class JsonType implements TypeInterface
{
    /**
     * @var positive-int
     */
    public const DEFAULT_JSON_DEPTH = 512;

    /**
     * @var bool
     */
    public const DEFAULT_JSON_AS_ARRAY = true;

    /**
     * @param bool $associative
     * @param int<1, max> $depth
     */
    public function __construct(
        public readonly bool $associative = self::DEFAULT_JSON_AS_ARRAY,
        public readonly int $depth = self::DEFAULT_JSON_DEPTH,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): string
    {
        if (\is_resource($value)) {
            $value = \stream_get_contents($value);
        }

        if (!\is_string($value)) {
            throw ParseException::fromInvalidType('string (json)', $value);
        }

        return \json_decode($value, $this->associative, $this->depth, \JSON_THROW_ON_ERROR);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): string
    {
        if (\is_resource($value)) {
            throw SerializeException::fromInvalidType('scalar or \JsonSerializable', $value);
        }

        return \json_encode($value, \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION, $this->depth);
    }
}
