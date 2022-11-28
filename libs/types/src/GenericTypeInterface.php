<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TPHPValue of mixed
 * @template TDatabaseValue of mixed
 * @template TWrappingType of TypeInterface
 *
 * @template-extends TypeInterface<TPHPValue, TDatabaseValue>
 */
interface GenericTypeInterface extends TypeInterface
{
    /**
     * @return TWrappingType
     */
    public function getType(): TypeInterface;
}
