<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Literal\StringLiteralNode;

final class NamedFieldNode extends GenericFieldNode
{
    public function __construct(
        public readonly StringLiteralNode $name,
        FieldNodeInterface $of,
    ) {
        parent::__construct($of);
    }

    public function __toString(): string
    {
        return \sprintf('%s', $this->name->value);
    }
}
