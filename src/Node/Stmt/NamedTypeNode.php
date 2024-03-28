<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode;

class NamedTypeNode extends TypeStatement
{
    public Name $name;

    /**
     * @param Name|Identifier|non-empty-string $name
     */
    public function __construct(
        Name|Identifier|string $name,
        public ?ArgumentsListNode $arguments = null,
        public ?FieldsListNode $fields = null,
    ) {
        $this->name = $name instanceof Name ? $name : new Name($name);
    }

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
