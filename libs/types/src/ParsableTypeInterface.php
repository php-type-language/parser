<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TPHPValue of mixed
 * @template TDatabaseValue of mixed
 */
interface ParsableTypeInterface
{
    /**
     * @param TPHPValue $value
     *
     * @return TDatabaseValue
     */
    public function parse(mixed $value): mixed;
}
