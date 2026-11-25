<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser\StringParser;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal\SubParser\StringParser
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
    public const REPLACEMENT_CHAR = "\u{FFFD}";

    /**
     * Method for encoding an utf-8 character by its code.
     *
     * Codes above the last Unicode code point are encoded as a replacement
     * char. The result does not depend on the mbstring settings.
     *
     * @param int<0, max> $code
     */
    public static function render(int $code): string
    {
        if ($code > self::MAX_CODE_POINT) {
            return self::REPLACEMENT_CHAR;
        }

        if (0x80 > $code) {
            // @phpstan-ignore-next-line the code is in the ASCII range
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
