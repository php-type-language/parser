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

    public function toArray(): array
    {
        return [
            'kind' => TypeKind::CLASS_CONST_KIND,
            'class' => $this->class->toString(),
            'constant' => $this->constant?->toString(),
        ];
    }
}
