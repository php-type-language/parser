<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Kind;

enum ShapeFieldKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard shape
     * field type that has not been explicitly defined.
     *
     * @internal
     */
    case UNKNOWN = Kind::SHAPE_FIELD_KIND;

    /**
     * Defines a field with a key of the form `key`.
     */
    case NAMED_FIELD_KIND = Kind::SHAPE_FIELD_KIND + 1;

    /**
     * Defines a field with a key of the form `"key"`.
     */
    case STRING_FIELD_KIND = Kind::SHAPE_FIELD_KIND + 2;

    /**
     * Defines a field with a key of the form `42`.
     */
    case NUMERIC_FIELD_KIND = Kind::SHAPE_FIELD_KIND + 3;

    /**
     * Defines a field without key.
     */
    case IMPLICIT_FIELD_KIND = Kind::SHAPE_FIELD_KIND + 4;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
