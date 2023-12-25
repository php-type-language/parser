<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

use TypeLang\Parser\Node\SerializableInterface;
use TypeLang\Parser\Node\SerializableKind;

enum ConditionKind: int implements SerializableInterface
{
    use SerializableKind;

    case UNKNOWN = 0;
    case KIND_EQUAL = 1;
    case KIND_NOT_EQUAL = 2;
}
