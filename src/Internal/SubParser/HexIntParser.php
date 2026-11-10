<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\IntLiteralKind;

/**
 * Reads a hexadecimal integer.
 *
 * ```
 *  0x1F  -0XDE_AD
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class HexIntParser extends IntParser
{
    protected const RADIX = 16;

    protected const PREFIX_LENGTH = 2;

    protected const KIND = IntLiteralKind::Hexadecimal;
}
