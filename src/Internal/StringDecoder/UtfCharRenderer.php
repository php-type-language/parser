<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\StringDecoder;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class UtfCharRenderer
{
    /**
     * @var int<0, max>
     */
    private const MAX_CODE_POINT = 0x10FFFF;

    /**
     * @var non-empty-string
     */
    private const REPLACEMENT_CHAR = "\u{FFFD}";

    /**
     * Method for encoding an utf-8 character by its code.
     *
     * Codes above the last Unicode code point are encoded as a replacement
     * char because such a sequence cannot be expressed in utf-8.
     *
     * @param int<0, max> $code
     */
    public static function render(int $code): string
    {
        if ($code > self::MAX_CODE_POINT) {
            return self::REPLACEMENT_CHAR;
        }

        // @phpstan-ignore-next-line : PHPStan false-positive mb_chr evaluation
        if (\function_exists('\\mb_chr') && ($result = \mb_chr($code)) !== false) {
            return $result;
        }

        if (0x80 > $code) {
            // @phpstan-ignore-next-line : Code is valid
            return \chr($code);
        }

        if (0x800 > $code) {
            return \chr(0xC0 | $code >> 6)
                 . \chr(0x80 | $code & 0x3F);
        }

        if (0x10000 > $code) {
            return \chr(0xE0 | $code >> 12)
                 . \chr(0x80 | $code >> 6 & 0x3F)
                 . \chr(0x80 | $code & 0x3F);
        }

        return \chr(0xF0 | $code >> 18)
             . \chr(0x80 | $code >> 12 & 0x3F)
             . \chr(0x80 | $code >> 6 & 0x3F)
             . \chr(0x80 | $code & 0x3F);
    }
}
