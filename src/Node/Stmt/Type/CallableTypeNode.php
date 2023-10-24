<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Type\Callable\ArgumentsListNode;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public readonly Name $name,
        public readonly ArgumentsListNode $arguments,
        public readonly ?TypeStatement $type = null,
    ) {}
}
