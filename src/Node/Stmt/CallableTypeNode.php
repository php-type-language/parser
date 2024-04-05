<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\ParametersListNode;

class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public Name $name,
        public ParametersListNode $parameters = new ParametersListNode(),
        public ?TypeStatement $type = null,
    ) {}

    public function jsonSerialize(): array
    {
        return \array_filter([
            'kind' => TypeKind::CALLABLE_KIND,
            'name' => $this->name->toString(),
            'parameters' => $this->parameters,
            'type' => $this->type,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
