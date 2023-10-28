<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Statement;

final class NamespaceNode extends Statement
{
    public function __construct(
        public readonly Name $name,
        public readonly NamespaceType $type,
        public ?DefinitionsListNode $definitions = null,
    ) {}
}
