<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class NamedFieldNode extends ExplicitFieldNode
{
    public Identifier $key;

    /**
     * @param Identifier|non-empty-string $key
     */
    public function __construct(
        Identifier|string $key,
        TypeStatement $of,
        bool $optional = false,
    ) {
        $this->key = \is_string($key) ? new Identifier($key) : $key;

        parent::__construct($of, $optional);
    }

    public function getKey(): string
    {
        /** @var non-empty-string */
        return $this->key->toString();
    }

    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'key' => $this->key->toString(),
            'kind' => ShapeFieldKind::NAMED_FIELD_KIND,
        ];
    }
}
