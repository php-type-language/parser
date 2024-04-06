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
     * @see Kind::LITERAL_KIND
     */
    case UNKNOWN = /*Kind::LITERAL_KIND*/0x0400;

    /**
     * Denotes any {@see bool} type consisting of the
     * literal {@see true} and {@see false} values.
     *
     * @see Kind::LITERAL_KIND
     */
    case BOOL_KIND = /*Kind::LITERAL_KIND*/0x0400 + 1;

    /**
     * Denotes any {@see float} type.
     *
     * @see Kind::LITERAL_KIND
     */
    case FLOAT_KIND = /*Kind::LITERAL_KIND*/0x0400 + 2;

    /**
     * Denotes any {@see int} type.
     *
     * @see Kind::LITERAL_KIND
     */
    case INT_KIND = /*Kind::LITERAL_KIND*/0x0400 + 3;

    /**
     * Denotes any {@see null} type.
     *
     * @see Kind::LITERAL_KIND
     */
    case NULL_KIND = /*Kind::LITERAL_KIND*/0x0400 + 4;

    /**
     * Denotes any {@see string} type.
     *
     * @see Kind::LITERAL_KIND
     */
    case STRING_KIND = /*Kind::LITERAL_KIND*/0x0400 + 5;

    /**
     * Denotes any variable (for example function
     * argument/parameter) type.
     *
     * @see Kind::LITERAL_KIND
     */
    case VARIABLE_KIND = /*Kind::LITERAL_KIND*/0x0400 + 6;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        /** @var int<0, max> */
        return $this->value;
    }
}
