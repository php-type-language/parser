<?php

declare(strict_types=1);

namespace Hyper\Type;

/**
 * @template-covariant TLiteral
 */
interface LiteralTypeInterface extends TypeInterface
{
    /**
     * @return TLiteral
     */
    public function getValue(): mixed;
}
