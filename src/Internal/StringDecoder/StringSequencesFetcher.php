<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\StringDecoder;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class StringSequencesFetcher
{
    /**
     * Sequences with a constant replacement.
     *
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    private const ESCAPED_CHARS = [
        '\n' => "\n",
        '\r' => "\r",
        '\t' => "\t",
        '\v' => "\v",
        '\e' => "\e",
        '\f' => "\f",
        '\$' => '$',
        '\\\\' => '\\',
    ];

    /**
     * @var non-empty-string
     */
    private const NUMERIC_PREFIX_PATTERN = '/\\\\[uxX0-7]/';

    /**
     * @var non-empty-string
     */
    private const NUMERIC_SEQUENCE_PATTERN = '/\\\\(?:u\{([0-9a-fA-F]+)}|[xX]([0-9a-fA-F]{1,2})|([0-7]{1,3}))/';

    /**
     * Returns a "sequence => replacement" map of all constant sequences along
     * with each hexadecimal (like a "\xFF"), octal (like a "\101") and utf-8
     * (like a "\u{FFFF}") sequence occurred in the past string.
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     *
     * @return non-empty-array<string, string>
     */
    public static function get(string $value): array
    {
        if (@\preg_match(self::NUMERIC_PREFIX_PATTERN, $value) !== 1) {
            return self::ESCAPED_CHARS;
        }

        $count = @\preg_match_all(self::NUMERIC_SEQUENCE_PATTERN, $value, $matches, \PREG_SET_ORDER);

        if ($count === false || $count === 0) {
            return self::ESCAPED_CHARS;
        }

        $result = self::ESCAPED_CHARS;

        /** @var list<array{0: non-empty-string, 1: string, 2?: string, 3?: string}> $matches */
        foreach ($matches as $match) {
            if (isset($result[$match[0]])) {
                continue;
            }

            // A unicode sequence, like a "\u{FFFF}"
            if ($match[1] !== '') {
                /** @var int<0, max> $code */
                $code = (int) \hexdec($match[1]);

                $result[$match[0]] = UtfCharRenderer::render($code);

                continue;
            }

            // A hexadecimal sequence, like a "\xFF"
            if (($match[2] ?? '') !== '') {
                // @phpstan-ignore-next-line : A hexdec returns int<0, 255>
                $result[$match[0]] = \chr((int) \hexdec($match[2] ?? ''));

                continue;
            }

            // An octal sequence, like a "\101". Overflowed sequences
            // (greater than a "\377") are truncated, like in PHP itself.
            $result[$match[0]] = \chr(((int) \octdec($match[3] ?? '')) & 0xFF);
        }

        return $result;
    }
}
