<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

class ClassConstNode extends ClassConstMaskNode
{
    /**
     * @param Identifier|non-empty-string $constant
     */
    public function __construct(Name $class, Identifier|string $constant)
    {
        parent::__construct($class, $constant);
    }

    public function jsonSerialize(): array
    {
        return \array_filter([
            'kind' => TypeKind::CLASS_CONST_KIND,
            'class' => $this->class->toString(),
            'constant' => $this->constant?->toString(),
        ], static fn (mixed $value): bool => $value !== null);
    }
}
