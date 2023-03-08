<?php

declare(strict_types=1);

namespace Hyper\Type;

class FloatLiteral extends FloatType implements LiteralTypeInterface
{
    public function __construct(
        public readonly float $value,
    ) {
        parent::__construct($this->value, $this->value);
    }
}
