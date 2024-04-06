<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;

class ConstMaskNode extends TypeStatement implements \Stringable
{
    public function __construct(
        public Name $name,
    ) {}

    public function __toString(): string
    {
        return $this->name->toString() . '*';
    }
}
