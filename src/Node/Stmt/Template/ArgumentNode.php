<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

class ArgumentNode extends Node
{
    public ?Identifier $hint;

    /**
     * @param Identifier|non-empty-string|null $hint
     */
    public function __construct(
        public TypeStatement $value,
        Identifier|string|null $hint = null,
    ) {
        $this->hint = \is_string($hint) ? new Identifier($hint) : $hint;
    }

    public function jsonSerialize(): array
    {
        return \array_filter([
            'hint' => $this->hint,
            'value' => $this->value,
        ], static fn(mixed $value): bool => $value !== null);
    }
}
