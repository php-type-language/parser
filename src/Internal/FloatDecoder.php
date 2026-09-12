<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

final class FloatDecoder
{
    public static function decode(string $value): float
    {
        return (float) \str_replace('_', '', $value);
    }
}
