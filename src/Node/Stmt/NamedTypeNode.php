<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\ArgumentsListNode;
use TypeLang\Parser\Node\Stmt\Template\ParametersListNode;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class NamedTypeNode extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?ParametersListNode $parameters = null,
        public readonly ?ArgumentsListNode $arguments = null,
    ) {}
}
