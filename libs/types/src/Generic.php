<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-covariant T of TypeInterface
 * @template-implements GenericTypeInterface<T>
 */
abstract class Generic implements GenericTypeInterface
{
    /**
     * @param T $value
     */
    public function __construct(
        public readonly TypeInterface $value,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): TypeInterface
    {
        return $this->value;
    }
}
