<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class NamedFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public Identifier $key,
        TypeStatement $of,
        bool $optional = false,
    ) {
        parent::__construct($of, $optional);
    }

    public function getKey(): string
    {
        /** @var non-empty-string */
        return $this->key->toString();
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'key' => $this->key->toString(),
            'kind' => ShapeFieldKind::NAMED_FIELD_KIND,
        ];
    }
}
