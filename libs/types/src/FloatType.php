<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Attribute\Usage;
use Hyper\Type\Literal\FloatLiteral;
use Hyper\Type\Literal\IntLiteral;
use Hyper\Type\Marker\MaxMarker;
use Hyper\Type\Marker\MinMarker;

/**
 * @template TMin of float
 * @template TMax of float
 */
#[Usage('<0>'), Usage('<0, 0>')]
#[Usage('<0, max>'), Usage('<min, 0>')]
class FloatType extends Type implements ScalarTypeInterface
{
    /**
     * @var TMin
     */
    public readonly float $min;

    /**
     * @var TMax
     */
    public readonly float $max;

    /**
     * @param TMin|MinMarker|MaxMarker|FloatLiteral<TMin> $min
     * @param TMax|MinMarker|MaxMarker|FloatLiteral<TMax> $max
     */
    public function __construct(
        float|MinMarker|MaxMarker|FloatLiteral $min = \PHP_FLOAT_MIN,
        float|MinMarker|MaxMarker|FloatLiteral $max = \PHP_FLOAT_MAX,
    ) {
        $this->min = $this->castToFloat($min);
        $this->max = $this->castToFloat($max);

        assert($this->min <= $this->max,
            'Min must be less or equal than max in range parameters');
        assert( (!\is_nan($this->min) && !\is_nan($this->max)) || (\is_nan($this->min) && \is_nan($this->max)),
            'When specifying the edge case NaN, both min and max must contain NaN values');
    }

    private function castToFloat(float|MinMarker|MaxMarker|IntLiteral $value): float
    {
        return match (true) {
            \is_float($value) => $value,
            $value instanceof MinMarker => \PHP_FLOAT_MIN,
            $value instanceof MaxMarker => \PHP_FLOAT_MAX,
            default => $value->getValue(),
        };
    }
}
