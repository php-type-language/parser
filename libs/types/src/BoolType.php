<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Factory\Singleton;
use Hyper\Type\Literal\FalseLiteral;
use Hyper\Type\Literal\TrueLiteral;
use Hyper\Type\Variadic\UnionType;

class BoolType extends UnionType implements ScalarTypeInterface
{
    use Singleton;

    public function __construct()
    {
        parent::__construct(TrueLiteral::getInstance(), FalseLiteral::getInstance());
    }
}
