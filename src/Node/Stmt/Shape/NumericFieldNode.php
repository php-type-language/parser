<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class NumericFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public IntLiteralNode $key,
        TypeStatement $of,
        bool $optional = false,
    ) {
        parent::__construct($of, $optional);
    }

    public function getKey(): int
    {
        return $this->key->getValue();
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'key' => $this->key->getValue(),
            'kind' => ShapeFieldKind::NUMERIC_FIELD_KIND,
        ];
    }
}
