<?php

declare(strict_types=1);

namespace Hyper\Type\EnumType;

enum Format
{
    case BACKED_INT;
    case BACKED_STRING;
    case UNIT;
    case UNKNOWN;

    /**
     * @param \ReflectionEnum $enum
     *
     * @return static
     */
    public static function fromReflection(\ReflectionEnum $enum): self
    {
        $type = $enum->getBackingType();

        if ($type === null) {
            return self::UNIT;
        }

        if (!$type instanceof \ReflectionNamedType) {
            return self::UNKNOWN;
        }

        return match ($type->getName()) {
            'string' => self::BACKED_STRING,
            'int' => self::BACKED_INT,
            default => self::UNKNOWN,
        };
    }
}
