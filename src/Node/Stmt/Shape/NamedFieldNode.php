<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
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
        ?AttributeGroupsListNode $attributes = null,
    ) {
        $this->key = \is_string($key) ? new Identifier($key) : $key;

        parent::__construct($of, $optional, $attributes);
    }

    public function getKey(): string
    {
        return $this->key->toString();
    }
}
