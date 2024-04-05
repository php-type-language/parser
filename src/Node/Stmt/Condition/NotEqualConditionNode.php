<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Condition;

final class NotEqualConditionNode extends Condition
{
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'kind' => ConditionKind::KIND_NOT_EQUAL,
        ];
    }
}
