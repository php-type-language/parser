<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

use TypeLang\Parser\Node\Statement;
use TypeLang\Parser\Node\Stmt\TypeStatement;

abstract class Condition extends Statement
{
    public function __construct(
        public readonly TypeStatement $subject,
        public readonly TypeStatement $target,
    ) {}
}
