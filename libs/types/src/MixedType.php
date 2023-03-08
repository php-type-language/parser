<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Factory\Singleton;
use Hyper\Type\Literal\NullLiteral;
use Hyper\Type\Variadic\UnionType;

class MixedType extends UnionType
{
    use Singleton;

    public function __construct()
    {
        parent::__construct(
            ScalarType::getInstance(),
            NullLiteral::getInstance(),
            ArrayType::getInstance(),
            ObjectType::getInstance(),
        );
    }
}
