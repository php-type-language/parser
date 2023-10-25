<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Type\TypeStatement;

final class StringNamedFieldNode extends FieldNode
{
    public function __construct(
        public readonly StringLiteralNode $name,
        TypeStatement $of,
        bool $optional = false,
    ) {
        parent::__construct($of, $optional);
    }
}
