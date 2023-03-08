<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

use Hyper\Type\IntType;
use Hyper\Type\LiteralTypeInterface;

/**
 * @template TInt of int
 * @template-implements LiteralTypeInterface<TInt>
 */
class IntLiteral extends IntType implements LiteralTypeInterface
{
    /**
     * @param TInt $value
     */
    public function __construct(
        public readonly int $value,
    ) {
        parent::__construct($this->value, $this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
