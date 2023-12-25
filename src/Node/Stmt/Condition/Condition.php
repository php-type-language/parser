<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

use TypeLang\Parser\Node\Statement;
use TypeLang\Parser\Node\Stmt\TypeStatement;

abstract class Condition extends Statement
{
    public function __construct(
        public TypeStatement $subject,
        public TypeStatement $target,
    ) {}

    public function toArray(): array
    {
        return [
            'kind' => ConditionKind::UNKNOWN,
            'subject' => $this->subject->toArray(),
            'target' => $this->target->toArray(),
        ];
    }
}
