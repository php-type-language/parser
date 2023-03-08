<?php

declare(strict_types=1);

namespace Hyper\Type;

class IntLiteral extends IntType implements LiteralTypeInterface
{
    public function __construct(
        public readonly int $value,
    ) {
        parent::__construct($this->value, $this->value);
    }
}
