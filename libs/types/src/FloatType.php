<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template TMin of float
 * @template TMax of float
 */
class FloatType extends Scalar
{
    /**
     * @param TMin $min
     * @param TMax $max
     */
    public function __construct(
        public readonly float $min = \PHP_FLOAT_MIN,
        public readonly float $max = \PHP_FLOAT_MAX,
    ) {
        assert($this->min <= $this->max, 'Min must be less or equal than max in range parameters');
    }
}
