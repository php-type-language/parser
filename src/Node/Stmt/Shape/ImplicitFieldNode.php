<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

class ImplicitFieldNode extends FieldNode
{
    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'kind' => ShapeFieldKind::IMPLICIT_FIELD_KIND,
        ];
    }
}
