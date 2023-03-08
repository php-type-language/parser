<?php

declare(strict_types=1);

namespace Hyper\Type;

class ArrayType implements GenericTypeInterface
{
    public function __construct(
        public readonly TypeInterface $value = new MixedType(),
        public readonly TypeInterface $key = new ArrayKey(),
    ) {
    }

    public function getKeyType(): TypeInterface
    {
        return $this->key;
    }

    public function getType(): TypeInterface
    {
        return $this->value;
    }
}
