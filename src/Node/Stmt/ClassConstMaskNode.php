<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

class ClassConstMaskNode extends TypeStatement
{
    public ?Identifier $constant;

    /**
     * @param Identifier|non-empty-string|null $constant
     */
    public function __construct(
        public Name $class,
        Identifier|string|null $constant = null,
    ) {
        $this->constant = \is_string($constant) ? new Identifier($constant) : $constant;
    }

    public function jsonSerialize(): array
    {
        return \array_filter([
            'kind' => TypeKind::CLASS_CONST_MASK_KIND,
            'class' => $this->class->toString(),
            'constant' => $this->constant?->toString(),
        ], static fn (mixed $value): bool => $value !== null);
    }
}
