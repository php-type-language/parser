<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\SerializableInterface;
use TypeLang\Parser\Node\SerializableKind;

enum TypeKind: int implements SerializableInterface
{
    use SerializableKind;

    case UNKNOWN = 0;
    case TYPE_KIND = 1;
    case CALLABLE_KIND = 2;
    case CLASS_CONST_MASK_KIND = 3;
    case CLASS_CONST_KIND = 4;
    case CONST_MASK_KIND = 5;
    case LOGICAL_INTERSECTION_KIND = 6;
    case LOGICAL_UNION_KIND = 7;
    case NULLABLE_KIND = 8;
    case TERNARY_KIND = 9;
    case LIST_KIND = 10;
}
