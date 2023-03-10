<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt\Callable;

/**
 * @internal This is an internal library enum, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
enum Modifier
{
    case OPTIONAL;
    case VARIADIC;
}
