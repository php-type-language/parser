<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

use TypeLang\Parser\Node\Kind;

enum ConditionKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard condition
     * that has not been explicitly defined.
     *
     * @internal
     */
    case UNKNOWN = Kind::CONDITION_KIND;

    /**
     * Indicates an equivalent comparison condition.
     */
    case KIND_EQUAL = Kind::CONDITION_KIND + 1;

    /**
     * Indicates an non-equivalent comparison condition.
     */
    case KIND_NOT_EQUAL = Kind::CONDITION_KIND + 2;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
