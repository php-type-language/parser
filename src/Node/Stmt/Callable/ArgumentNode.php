<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ArgumentNode extends Node
{
    public function __construct(
        public readonly Statement $type,
        public readonly ?Modifier $modifier = null,
    ) {}
}
