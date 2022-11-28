<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\EnumType\Format;
use Hyper\Type\Exception\ParseException;
use Hyper\Type\Exception\SerializeException;
use Hyper\Type\Exception\TypeException;

/**
 * @template TEnum of \BackedEnum
 * @template-implements TypeInterface<TEnum, string|int>
 */
final class EnumType implements TypeInterface
{
    /**
     * @var Format
     */
    private readonly Format $format;

    /**
     * @param class-string<TEnum> $class
     */
    public function __construct(
        private readonly string $class,
    ) {
        try {
            $reflection = new \ReflectionEnum($this->class);
        } catch (\ReflectionException) {
            throw new TypeException('Enum ' . $this->class . ' not found');
        }

        $this->format = Format::fromReflection($reflection);

        if ($this->format === Format::UNKNOWN) {
            throw new TypeException('Unsupported ' . $this->class . ' enum type');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function parse(mixed $value): \UnitEnum
    {
        if ($this->format === Format::UNIT) {
            if (\is_string($value)) {
                return \constant($this->class . '::' . $value);
            }

            throw ParseException::fromInvalidType('string case name of enum<' . $this->class . '>', $value);
        }

        if ($this->format === Format::BACKED_STRING) {
            if (\is_string($value)) {
                return $this->class::tryFrom($value);
            }

            throw ParseException::fromInvalidType('string value of enum<' . $this->class . '>', $value);
        }

        if (\is_int($value)) {
            return $this->class::tryFrom($value);
        }

        throw ParseException::fromInvalidType('int value of enum<' . $this->class . '>', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $value): int|string
    {
        if (!$value instanceof $this->class) {
            throw SerializeException::fromInvalidType('enum<' . $this->class . '>', $value);
        }

        if ($this->format === Format::UNIT) {
            return $value->name;
        }

        return $value->value;
    }
}
