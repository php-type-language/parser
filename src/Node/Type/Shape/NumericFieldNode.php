<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Type\TypeStatement;

final class NumericFieldNode extends ExplicitFieldNode
{
    public function __construct(
        public readonly IntLiteralNode $index,
        TypeStatement $of,
        bool $optional = false,
    ) {
        parent::__construct($of, $optional);
    }

    public function getIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->index->getRawValue();
    }
}
