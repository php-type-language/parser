<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\ParametersListNode;

class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public readonly Name $name,
        public readonly ParametersListNode $parameters = new ParametersListNode(),
        public readonly ?TypeStatement $type = null,
    ) {}
}
