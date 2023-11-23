<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class NamedFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public Identifier $name,
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
