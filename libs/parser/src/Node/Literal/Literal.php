<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
abstract class Literal extends Statement
{
    /**
     * @return mixed
     */
    abstract public function getValue(): mixed;
}
