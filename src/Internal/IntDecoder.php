<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use TypeLang\Parser\Internal\IntDecoder\BigIntValue;

final class IntDecoder
{
    public static function decode(string $value): BigIntValue
    {
        /** @var array{numeric-string, bool} $pair */
        $pair = self::split($value);

        // is negative number
        if ($pair[1]) {
            $inverse = '-' . $pair[0];

            // special case: int64 min value
            if ((string) \PHP_INT_MIN === $inverse) {
                return new BigIntValue(\PHP_INT_MIN, $inverse);
            }

            // @phpstan-ignore-next-line : PHPStan false-positive
            return new BigIntValue((int) $inverse, $inverse);
        }

        return new BigIntValue((int) $pair[0], $pair[0]);
    }

    /**
     * @return array{numeric-string, bool}
     */
    private static function split(string $value): array
    {
        /** @var numeric-string $value */
        $value = \str_replace('_', '', $value);

        // A sign is written apart from the digits it belongs to, and a
        // leading plus says nothing the absence of a sign does not
        if (($isNegative = ($value[0] === '-')) || $value[0] === '+') {
            /** @var numeric-string $value */
            $value = \substr($value, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($value[0] === '0' && isset($value[1])) {
            return [self::decodeNonDecimalValue($value), $isNegative];
        }

        return [$value, $isNegative];
    }

    /**
     * @param non-empty-string $value
     * @return numeric-string
     */
    private static function decodeNonDecimalValue(string $value): string
    {
        /** @var numeric-string */
        return match ($value[1] ?? '') {
            // hexadecimal
            'x', 'X' => \base_convert(\substr($value, 2), 16, 10),
            // binary
            'b', 'B' => \base_convert(\substr($value, 2), 2, 10),
            // octal
            'o', 'O' => \base_convert(\substr($value, 2), 8, 10),
            // octal (legacy)
            default => \base_convert($value, 8, 10),
        };
    }
}
