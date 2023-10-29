<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of mixed
 * @template TInputValue of string
 */
interface ParsableLiteralNodeInterface
{
    /**
     * Parse raw literal string value.
     *
     * @param TInputValue $value
     *
     * @return self<TValue>
     */
    public static function parse(string $value): self;
}
