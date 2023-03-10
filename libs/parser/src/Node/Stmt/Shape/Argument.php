<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt\Shape;

use Hyper\Parser\Node\Literal\LiteralStmt;
use Hyper\Parser\Node\Literal\StringLiteralStmt;
use Hyper\Parser\Node\Node;
use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class Argument extends Node
{
    public function __construct(
        public readonly Statement $value,
        public readonly ?StringLiteralStmt $name = null,
        public readonly bool $optional = false,
    ) {
    }
}
