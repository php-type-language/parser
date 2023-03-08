<?php

declare(strict_types=1);

namespace Hyper\Type;

abstract class BoolLiteral extends BoolType implements LiteralTypeInterface
{
    public function __construct(
        public readonly bool $value,
    ) {
    }
}
