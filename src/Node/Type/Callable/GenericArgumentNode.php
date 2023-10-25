<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

abstract class GenericArgumentNode extends Node implements ArgumentNodeInterface
{
    public function __construct(
        public readonly ArgumentNodeInterface $of,
    ) {}

    public function is(string $class): bool
    {
        return $this instanceof $class || $this->of->is($class);
    }

    public function getType(): TypeStatement
    {
        return $this->of->getType();
    }
}
