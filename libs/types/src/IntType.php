<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TMin of int
 * @template TMax of int
 */
class IntType extends Scalar
{
    /**
     * @param TMin $min
     * @param TMax $max
     */
    public function __construct(
        public readonly int $min = \PHP_INT_MIN,
        public readonly int $max = \PHP_INT_MAX,
    ) {
        assert($this->min <= $this->max, 'Min must be less or equal than max in range parameters');
    }
}
