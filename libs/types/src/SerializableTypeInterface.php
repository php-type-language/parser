<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TDatabaseValue of mixed
 * @template TPHPValue of mixed
 */
interface SerializableTypeInterface
{
    /**
     * @param TDatabaseValue $value
     *
     * @return TPHPValue
     */
    public function serialize(mixed $value): mixed;
}
