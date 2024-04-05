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
        return [
            'kind' => TypeKind::CALLABLE_KIND,
            'name' => $this->name->toString(),
            'parameters' => $this->parameters->jsonSerialize(),
            'type' => $this->type?->jsonSerialize(),
        ];
    }
}
