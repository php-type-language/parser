<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Factory\Singleton;
use Hyper\Type\Variadic\UnionType;

class ArrayKey extends UnionType implements ScalarTypeInterface
{
    use Singleton;

    public function __construct()
    {
        parent::__construct(IntType::getInstance(), StringType::getInstance());
    }
}
