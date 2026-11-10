<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\IntLiteralKind;

/**
 * Reads a binary integer.
 *
 * ```
 *  0b101  -0B10_10
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class BinIntParser extends IntParser
{
    protected const RADIX = 2;

    protected const PREFIX_LENGTH = 2;

    protected const KIND = IntLiteralKind::Binary;
}
