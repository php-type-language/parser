<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

final class NotEqualConditionNode extends Condition
{
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => ConditionKind::KIND_NOT_EQUAL,
        ];
    }
}
