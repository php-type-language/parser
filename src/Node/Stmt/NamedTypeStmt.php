<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\Arguments;
use TypeLang\Parser\Node\Stmt\Template\Parameters;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class NamedTypeStmt extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?Parameters $parameters = null,
        public readonly ?Arguments $arguments = null,
    ) {
    }
}
