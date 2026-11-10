<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\IntLiteralKind;

/**
 * Reads an octal integer with the `0o` prefix.
 *
 * ```
 *  0o17  -0O6_5_5
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class OctIntParser extends IntParser
{
    protected const RADIX = 8;

    protected const PREFIX_LENGTH = 2;

    protected const KIND = IntLiteralKind::Octal;
}
