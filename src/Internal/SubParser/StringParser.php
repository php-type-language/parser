<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\StringLiteralNode;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements SubParserInterface<StringLiteralNode>
 */
abstract class StringParser implements SubParserInterface
{
    /**
     * Decodes the sequences of an unpacked body: A `\\` in a single-quoted
     * one, and special chars (like a `\n`), hexadecimal (like a `\xFF`),
     * octal (like a `\101`) and utf-8 (like a `\u{FFFF}`) ones in a
     * double-quoted one.
     */
    abstract protected static function decode(string $value): string;

    /**
     * Strips the quotes and unescapes the ones inside: A `"\"a\""` gives
     * the `"a"` and a `'\'a\''` gives the `'a'`.
     *
     * Every other sequence is left to the {@see decode()}.
     */
    abstract protected static function unpack(string $value): string;
}
