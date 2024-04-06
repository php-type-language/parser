<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

use TypeLang\Parser\Node\Kind;

enum LiteralKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard literal
     * value that has not been explicitly defined.
     *
     * @internal
     */
    case UNKNOWN = Kind::LITERAL_KIND;

    /**
     * Denotes any {@see bool} type consisting of the
     * literal {@see true} and {@see false} values.
     */
    case BOOL_KIND = Kind::LITERAL_KIND + 1;

    /**
     * Denotes any {@see float} type.
     */
    case FLOAT_KIND = Kind::LITERAL_KIND + 2;

    /**
     * Denotes any {@see int} type.
     */
    case INT_KIND = Kind::LITERAL_KIND + 3;

    /**
     * Denotes any {@see null} type.
     */
    case NULL_KIND = Kind::LITERAL_KIND + 4;

    /**
     * Denotes any {@see string} type.
     */
    case STRING_KIND = Kind::LITERAL_KIND + 5;

    /**
     * Denotes any variable (for example function
     * argument/parameter) type.
     */
    case VARIABLE_KIND = Kind::LITERAL_KIND + 6;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        /** @var int<0, max> */
        return $this->value;
    }
}
