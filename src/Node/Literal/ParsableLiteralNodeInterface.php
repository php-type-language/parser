<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

interface ParsableLiteralNodeInterface
{
    /**
     * Parse raw literal string value.
     */
    public static function parse(string $value): self;
}
