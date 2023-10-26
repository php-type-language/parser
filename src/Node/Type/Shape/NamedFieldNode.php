<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Type\TypeStatement;

final class NamedFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public readonly Identifier $name,
        TypeStatement $of,
        bool $optional = false,
    ) {
        parent::__construct($of, $optional);
    }

    public function getIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->name->toString();
    }
}
