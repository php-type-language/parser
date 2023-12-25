<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode;

class NamedTypeNode extends TypeStatement
{
    public function __construct(
        public Name $name,
        public ?ArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {}

    public function toArray(): array
    {
        $result = [
            'kind' => TypeKind::TYPE_KIND,
            'name' => $this->name->toString(),
        ];

        if ($this->arguments !== null) {
            $arguments = [];

            foreach ($this->arguments as $argument) {
                $arguments[] = $argument->value->toArray();
            }

            $result['arguments'] = $arguments;
        }

        if ($this->fields !== null) {
            $result['fields'] = $this->fields->toArray();
        }

        return $result;
    }
}
