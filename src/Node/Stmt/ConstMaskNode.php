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

    public function jsonSerialize(): array
    {
        return [
            'kind' => TypeKind::CONST_MASK_KIND,
            'name' => $this->name->jsonSerialize(),
        ];
    }
}
