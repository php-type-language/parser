<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Literal\IntLiteralNode;

final class NumericFieldNode extends GenericFieldNode
{
    public function __construct(
        public readonly IntLiteralNode $index,
        FieldNodeInterface $of,
    ) {
        parent::__construct($of);
    }

    public function __toString(): string
    {
        return \sprintf('%s', $this->index->value);
    }
}
