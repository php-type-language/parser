<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

class ClassConstMaskNode extends TypeStatement
{
    public function __construct(
        public readonly Name $class,
        public readonly ?Identifier $constant = null,
    ) {}
}
