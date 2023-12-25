<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

class ImplicitFieldNode extends FieldNode
{
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => ShapeFieldKind::IMPLICIT_FIELD_KIND,
        ];
    }
}
