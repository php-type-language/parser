<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Factory\Singleton;
use Hyper\Type\Variadic\UnionType;

class ScalarType extends UnionType implements ScalarTypeInterface
{
    use Singleton;

    public function __construct()
    {
        parent::__construct(
            BoolType::getInstance(),
            FloatType::getInstance(),
            IntType::getInstance(),
            StringType::getInstance(),
        );
    }
}
