<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

enum ConditionKind: int implements \JsonSerializable
{
    /**
     * Indicates an arbitrary and non-standard condition
     * that has not been explicitly defined.
     */
    case UNKNOWN = 0;

    /**
     * Indicates an equivalent comparison condition.
     */
    case KIND_EQUAL = 1;

    /**
     * Indicates an non-equivalent comparison condition.
     */
    case KIND_NOT_EQUAL = 2;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
