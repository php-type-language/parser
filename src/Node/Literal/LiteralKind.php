<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

enum LiteralKind: int implements \JsonSerializable
{
    case UNKNOWN = 0;
    case BOOL_KIND = 1;
    case FLOAT_KIND = 2;
    case INT_KIND = 3;
    case NULL_KIND = 4;
    case STRING_KIND = 5;
    case VARIABLE_KIND = 6;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
