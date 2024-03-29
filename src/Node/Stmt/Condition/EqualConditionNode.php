<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

final class EqualConditionNode extends Condition
{
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => ConditionKind::KIND_EQUAL,
        ];
    }
}
