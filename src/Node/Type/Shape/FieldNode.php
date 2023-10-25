<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

final class FieldNode extends Node implements FieldNodeInterface
{
    public function __construct(
        public readonly TypeStatement $value,
    ) {}

    public function is(string $class): bool
    {
        return $this instanceof $class;
    }

    public function getValue(): TypeStatement
    {
        return $this->value;
    }
}
