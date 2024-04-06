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
     * @see Kind::SHAPE_FIELD_KIND
     */
    case UNKNOWN = /*Kind::SHAPE_FIELD_KIND*/0x0200;

    /**
     * Defines a field with a key of the form `key`.
     *
     * @see Kind::SHAPE_FIELD_KIND
     */
    case NAMED_FIELD_KIND = /*Kind::SHAPE_FIELD_KIND*/0x0200 + 1;

    /**
     * Defines a field with a key of the form `"key"`.
     *
     * @see Kind::SHAPE_FIELD_KIND
     */
    case STRING_FIELD_KIND = /*Kind::SHAPE_FIELD_KIND*/0x0200 + 2;

    /**
     * Defines a field with a key of the form `42`.
     *
     * @see Kind::SHAPE_FIELD_KIND
     */
    case NUMERIC_FIELD_KIND = /*Kind::SHAPE_FIELD_KIND*/0x0200 + 3;

    /**
     * Defines a field without key.
     *
     * @see Kind::SHAPE_FIELD_KIND
     */
    case IMPLICIT_FIELD_KIND = /*Kind::SHAPE_FIELD_KIND*/0x0200 + 4;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
