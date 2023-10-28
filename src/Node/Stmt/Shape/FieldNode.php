<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

class FieldNode extends Node implements \Stringable
{
    public function __construct(
        public readonly TypeStatement $value,
        public bool $optional = false,
    ) {}

    public function is(string $class): bool
    {
        return $this instanceof $class;
    }

    public function getValue(): TypeStatement
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->optional ? 'optional' : 'required';
    }
}
