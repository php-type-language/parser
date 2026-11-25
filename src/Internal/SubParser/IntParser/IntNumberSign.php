<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser\IntParser;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal\SubParser
 */
enum IntNumberSign
{
    case None;
    case Minus;
    case Plus;
}
