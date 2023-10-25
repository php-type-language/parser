<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

use TypeLang\Parser\Node\Name;

class ConstMaskNode extends TypeStatement implements \Stringable
{
    public function __construct(
        public readonly Name $name,
    ) {}

    public function __toString(): string
    {
        return $this->name->toString() . '*';
    }
}
