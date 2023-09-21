<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\ArgumentsListNode;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class CallableTypeNode extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly ArgumentsListNode $arguments,
        public readonly ?Statement $type = null,
    ) {}
}
