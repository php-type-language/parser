<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

enum Visibility
{
    case PUBLIC;
    case PROTECTED;
    case PRIVATE;
}
