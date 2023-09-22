<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

class VariableLiteralNode extends StringLiteralNode
{
    /**
     * @param non-empty-string $value
     */
    public static function parse(string $value): static
    {
        assert(\strlen($value) >= 1);

        return static::createFromLiteralValue(\substr($value, 1));
    }
}
