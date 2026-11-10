<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\BaseConverter;

/**
 * Converts the base by the `ext-gmp` extension.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class GmpBaseConverter implements BaseConverterInterface
{
    public function isAvailable(): bool
    {
        return \extension_loaded('gmp');
    }

    public function toDecimal(string $digits, int $base): string
    {
        if ($base === self::OUTPUT_RADIX) {
            /** @phpstan-ignore-next-line : 10-base digits are numeric-string */
            return $digits;
        }

        /** @var numeric-string */
        return \gmp_strval(\gmp_init($digits, $base));
    }
}
