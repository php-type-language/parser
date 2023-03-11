<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ClassConstNode extends ClassConstMaskNode
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
