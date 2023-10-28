<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

class ArgumentNode extends Node
{
    public function __construct(
        public readonly TypeStatement $value
    ) {}
}
