<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class ClassConstStmt extends ClassConstMaskStmt
{
    /**
     * @param Name $class
     * @param non-empty-string $constant
     */
    public function __construct(
        Name $class,
        string $constant,
    ) {
        parent::__construct($class, $constant);
    }
}
