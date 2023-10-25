<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class GenericFieldNode extends Node implements FieldNodeInterface
{
    public function __construct(
        public readonly FieldNodeInterface $of,
    ) {
    }

    public function is(string $class): bool
    {
        return $this instanceof $class || $this->of->is($class);
    }

    public function getValue(): TypeStatement
    {
        return $this->of->getValue();
    }
}
