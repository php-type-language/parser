<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class GenericArgumentNode extends Node implements ArgumentNodeInterface
{
    public function __construct(
        public readonly ArgumentNodeInterface $of,
    ) {
    }

    public function is(string $class): bool
    {
        return $this instanceof $class || $this->of->is($class);
    }

    public function getType(): TypeStatement
    {
        return $this->of->getType();
    }
}
