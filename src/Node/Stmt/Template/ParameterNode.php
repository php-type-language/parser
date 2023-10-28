<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class ParameterNode extends Node
{
    public function __construct(
        public readonly Identifier $name,
        public ParameterVariance $variance = ParameterVariance::ANY,
        public ?TypeStatement $type = null,
    ) {}
}
