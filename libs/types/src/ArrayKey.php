<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Variadic\UnionType;

class ArrayKey extends UnionType
{
    public function __construct()
    {
        parent::__construct(new IntType(), new StringType());
    }
}
