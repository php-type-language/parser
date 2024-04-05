<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Statement;

abstract class TypeStatement extends Statement
{
    public function jsonSerialize(): array
    {
        return [
            'kind' => TypeKind::UNKNOWN,
        ];
    }
}
