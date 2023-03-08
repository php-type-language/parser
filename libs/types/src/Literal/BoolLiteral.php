<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

use Hyper\Type\BoolType;
use Hyper\Type\LiteralTypeInterface;

/**
 * @template-implements LiteralTypeInterface<bool>
 */
abstract class BoolLiteral extends BoolType implements LiteralTypeInterface
{
    public function __construct(
        public readonly bool $value,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): bool
    {
        return $this->value;
    }
}
