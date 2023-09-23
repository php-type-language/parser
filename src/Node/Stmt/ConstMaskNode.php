<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ConstMaskNode extends Statement
{
    public function __construct(
        public readonly Name $name,
    ) {}

    public function __toString(): string
    {
        return $this->name->name . '*';
    }
}
