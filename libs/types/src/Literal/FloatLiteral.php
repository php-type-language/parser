<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

use Hyper\Type\FloatType;
use Hyper\Type\LiteralTypeInterface;

/**
 * @template TFloat of float
 * @template-implements LiteralTypeInterface<TFloat>
 */
class FloatLiteral extends FloatType implements LiteralTypeInterface
{
    /**
     * @param TFloat $value
     */
    public function __construct(
        public readonly float $value,
    ) {
        parent::__construct($this->value, $this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
