<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

class ClassConstNode extends ClassConstMaskNode
{
    public function __construct(Name $class, Identifier $constant)
    {
        parent::__construct($class, $constant);
    }
}
