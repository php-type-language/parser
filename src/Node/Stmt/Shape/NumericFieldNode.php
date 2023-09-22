<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Literal\IntLiteralNode;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
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
