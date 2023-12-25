<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

class ClassConstMaskNode extends TypeStatement
{
    public function __construct(
        public Name $class,
        public ?Identifier $constant = null,
    ) {}

    public function toArray(): array
    {
        return [
            'kind' => TypeKind::CLASS_CONST_MASK_KIND,
            'class' => $this->class->toString(),
            'constant' => $this->constant?->toString(),
        ];
    }
}
