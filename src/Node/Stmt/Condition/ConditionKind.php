<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

use TypeLang\Parser\Node\Kind;

const CONDITION_KIND = Kind::CONDITION_KIND;

enum ConditionKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard condition
     * that has not been explicitly defined.
     *
     * @internal
     */
    case UNKNOWN = CONDITION_KIND;

    /**
     * Indicates an equivalent comparison condition.
     */
    case KIND_EQUAL = CONDITION_KIND + 1;

    /**
     * Indicates an non-equivalent comparison condition.
     */
    case KIND_NOT_EQUAL = CONDITION_KIND + 2;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
