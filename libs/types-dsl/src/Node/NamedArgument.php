<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node;

use Hyper\Type\DSL\Node\Literal\Literal;
use Hyper\Type\DSL\Node\Stmt\TypeStmt;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class NamedArgument extends Node
{
    /**
     * @param int<0, max> $offset
     * @param non-empty-string $name
     * @param Argument $argument
     */
    public function __construct(
        int $offset,
        public readonly string $name,
        public readonly Argument $argument,
    ) {
        parent::__construct($offset);
    }
}
