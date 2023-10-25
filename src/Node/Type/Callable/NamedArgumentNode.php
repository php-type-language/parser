<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Literal\VariableLiteralNode;

final class NamedArgumentNode extends GenericArgumentNode
{
    public function __construct(
        public readonly VariableLiteralNode $name,
        ArgumentNodeInterface $of,
    ) {
        parent::__construct($of);
    }

    public function __toString(): string
    {
        return $this->name->getRawValue();
    }
}
