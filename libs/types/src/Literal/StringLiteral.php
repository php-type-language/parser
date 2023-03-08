<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

use Hyper\Type\LiteralTypeInterface;
use Hyper\Type\StringType;

/**
 * @template-implements LiteralTypeInterface<string>
 */
class StringLiteral extends StringType implements LiteralTypeInterface
{
    public function __construct(
        public readonly string $value,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
