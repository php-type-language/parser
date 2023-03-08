<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Shape;

use Hyper\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class NamedArgument extends Argument
{
    /**
     * @param non-empty-string $name
     * @param Statement $value
     */
    public function __construct(
        public readonly string $name,
        Statement $value,
    ) {
        parent::__construct($value);
    }
}
