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
     * @see Kind::CONDITION_KIND
     */
    case UNKNOWN = /*Kind::CONDITION_KIND*/0x0300;

    /**
     * Indicates an equivalent comparison condition.
     *
     * @see Kind::CONDITION_KIND
     */
    case KIND_EQUAL = /*Kind::CONDITION_KIND*/0x0300 + 1;

    /**
     * Indicates an non-equivalent comparison condition.
     *
     * @see Kind::CONDITION_KIND
     */
    case KIND_NOT_EQUAL = /*Kind::CONDITION_KIND*/0x0300 + 2;

    /**
     * @return int<0, max>
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
