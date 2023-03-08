<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Factory\Singleton;

abstract class Type implements TypeInterface
{
    use Singleton;
}
