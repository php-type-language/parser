<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use TypeLang\Parser\Internal\StringDecoder\StringSequencesFetcher;

/**
 * @link https://www.php.net/manual/en/language.types.string.php
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 */
final class StringDecoder
{
    /**
     * Strips the quotes and unescapes the ones inside: A `"\"a\""` gives
     * the `"a"` and a `'\'a\''` gives the `'a'`.
     *
     * Every other sequence is left to the {@see decode()}.
     */
    public static function unpack(string $value, bool $isDoubleQuoted): string
    {
        if ($isDoubleQuoted) {
            return \strtr(\substr($value, 1, -1), ['\\"' => '"']);
        }

        return \strtr(\substr($value, 1, -1), ["\'" => "'"]);
    }

    /**
     * Decodes the sequences of an unpacked body: A `\\` in a single-quoted
     * one, and special chars (like a `\n`), hexadecimal (like a `\xFF`),
     * octal (like a `\101`) and utf-8 (like a `\u{FFFF}`) ones in a
     * double-quoted one.
     */
    public static function decode(string $value, bool $isDoubleQuoted): string
    {
        if ($isDoubleQuoted === false) {
            return \strtr($value, ['\\\\' => '\\']);
        }

        if (!\str_contains($value, '\\')) {
            return $value;
        }

        return \strtr($value, StringSequencesFetcher::get($value));
    }

    /**
     * Both of the above, in the order a literal is read in.
     */
    public static function unpackAndDecode(string $value, bool $isDoubleQuoted): string
    {
        return StringDecoder::decode(
            StringDecoder::unpack($value, $isDoubleQuoted),
            $isDoubleQuoted,
        );
    }
}
