<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Hyper\Type\DSL\Node\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
abstract class Literal extends Node
{
    /**
     * @return mixed
     */
    abstract public function getValue(): mixed;
}
