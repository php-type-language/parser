<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of mixed
 */
interface LiteralNodeInterface extends \Stringable
{
    /**
     * Returns a PHP representation of the literal value.
     *
     * @return TValue
     */
    public function getValue(): mixed;

    /**
     * Returns the original literal value specified in the token.
     */
    public function getRawValue(): string;

    /**
     * Returns the processed ({@see getValue()}) literal value as a string.
     */
    public function __toString(): string;
}
