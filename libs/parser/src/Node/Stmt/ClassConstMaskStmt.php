<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Stmt;

use Hyper\Parser\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class ClassConstMaskStmt extends Statement
{
    /**
     * @param non-empty-string|null $constant
     */
    public function __construct(
        public readonly Name $class,
        public readonly ?string $constant = null,
    ) {
    }
}
