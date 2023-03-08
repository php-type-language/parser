<?php

declare(strict_types=1);

namespace Hyper\Type\Literal;

use Hyper\Type\LiteralTypeInterface;
use Hyper\Type\Type;

/**
 * @template-implements LiteralTypeInterface<null>
 */
class NullLiteral extends Type implements LiteralTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getValue(): mixed
    {
        return null;
    }
}
