<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type\Template;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Type\TypeStatement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ParameterNode extends Node
{
    public function __construct(
        public readonly TypeStatement $value
    ) {}
}
