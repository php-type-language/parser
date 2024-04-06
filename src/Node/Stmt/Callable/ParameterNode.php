<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\Literal\VariableLiteralNode;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class ParameterNode extends Node implements \Stringable
{
    public function __construct(
        public ?TypeStatement $type = null,
        public ?VariableLiteralNode $name = null,
        public bool $output = false,
        public bool $variadic = false,
        public bool $optional = false,
    ) {
        assert($type !== null || $name !== null, new \TypeError(
            'Required indication of the type or name of the parameter (one of)',
        ));

        assert($variadic === false || $optional === false, new \TypeError(
            'Parameter cannot be both variable and optional (variadic parameter is already optional)',
        ));
    }

    public function is(string $class): bool
    {
        return $this instanceof $class;
    }

    public function getType(): ?TypeStatement
    {
        return $this->type;
    }

    public function __toString(): string
    {
        $result = [];

        if ($this->output) {
            $result[] = 'output';
        }

        if ($this->variadic) {
            $result[] = 'variadic';
        }

        if ($this->optional) {
            $result[] = 'optional';
        }

        if ($result === []) {
            return 'simple';
        }

        return \implode(', ', $result);
    }
}
