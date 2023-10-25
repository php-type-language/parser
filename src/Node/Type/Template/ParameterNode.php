<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Template;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

class ParameterNode extends Node
{
    public function __construct(
        public readonly TypeStatement $value
    ) {}
}
