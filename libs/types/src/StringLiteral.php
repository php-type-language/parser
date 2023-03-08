<?php

declare(strict_types=1);

namespace Hyper\Type;

class StringLiteral extends StringType implements LiteralTypeInterface
{
    public function __construct(
        public readonly string $value,
    ) {
    }
}
