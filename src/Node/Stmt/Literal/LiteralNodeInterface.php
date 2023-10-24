<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Literal;

/**
 * @template TValue of mixed
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
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
