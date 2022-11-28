<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TPHPValue of mixed
 * @template TDatabaseValue of mixed
 * @template TWrappingType of TypeInterface
 *
 * @template-extends GenericTypeInterface<TPHPValue, TDatabaseValue, TWrappingType>
 */
abstract class GenericType implements GenericTypeInterface
{
    /**
     * @param TWrappingType $type
     */
    public function __construct(
        protected readonly TypeInterface $type,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }
}
