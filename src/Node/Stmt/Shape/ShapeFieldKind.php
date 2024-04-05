<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

enum ShapeFieldKind: int implements \JsonSerializable
{
    case UNKNOWN = 0;

    /**
     * Defines a field with a key of the form `key`.
     */
    case NAMED_FIELD_KIND = 1;

    /**
     * Defines a field with a key of the form `"key"`.
     */
    case STRING_FIELD_KIND = 2;

    /**
     * Defines a field with a key of the form `42`.
     */
    case NUMERIC_FIELD_KIND = 3;

    /**
     * Defines a field without key.
     */
    case IMPLICIT_FIELD_KIND = 4;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
