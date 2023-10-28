<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\Literal\VariableLiteralNode;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class ArgumentNode extends Node implements \Stringable
{
    public function __construct(
        public readonly TypeStatement $type,
        public ?VariableLiteralNode $name = null,
        public bool $output = false,
        public bool $variadic = false,
        public bool $optional = false,
    ) {}

    public function is(string $class): bool
    {
        return $this instanceof $class;
    }

    public function getType(): TypeStatement
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
