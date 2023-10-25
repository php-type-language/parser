<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

final class ArgumentNode extends Node implements ArgumentNodeInterface
{
    public function __construct(
        public readonly TypeStatement $type,
    ) {}

    public function is(string $class): bool
    {
        return $this instanceof $class;
    }

    public function getType(): TypeStatement
    {
        return $this->type;
    }
}
