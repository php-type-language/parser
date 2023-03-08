<?php

declare(strict_types=1);

namespace Hyper\Type;

use Hyper\Type\Attribute\Usage;
use Hyper\Type\Literal\IntLiteral;
use Hyper\Type\Marker\MaxMarker;
use Hyper\Type\Marker\MinMarker;

/**
 * @template TMin of int
 * @template TMax of int
 */
#[Usage('<0>'), Usage('<0, 0>')]
#[Usage('<0, max>'), Usage('<min, 0>')]
class IntType extends Type implements ScalarTypeInterface
{
    /**
     * @var TMin
     */
    public readonly int $min;

    /**
     * @var TMax
     */
    public readonly int $max;

    /**
     * @param TMin|MinMarker|MaxMarker|IntLiteral<TMin> $min
     * @param TMax|MinMarker|MaxMarker|IntLiteral<TMax> $max
     */
    public function __construct(
        int|MinMarker|MaxMarker|IntLiteral $min = \PHP_INT_MIN,
        int|MinMarker|MaxMarker|IntLiteral $max = \PHP_INT_MAX,
    ) {
        $this->min = $this->castToInt($min);
        $this->max = $this->castToInt($max);

        assert($this->min <= $this->max, 'Min must be less or equal than max in range parameters');
    }

    private function castToInt(int|MinMarker|MaxMarker|IntLiteral $value): int
    {
        return match (true) {
            \is_int($value) => $value,
            $value instanceof MinMarker => \PHP_INT_MIN,
            $value instanceof MaxMarker => \PHP_INT_MAX,
            default => $value->getValue(),
        };
    }
}
