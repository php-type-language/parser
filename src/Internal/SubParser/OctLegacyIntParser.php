<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\IntLiteralKind;

/**
 * Reads an octal integer with a leading zero and no prefix, a lone zero too.
 *
 * ```
 *  017  -0_42  0
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class OctLegacyIntParser extends IntParser
{
    protected const RADIX = 8;

    protected const PREFIX_LENGTH = 0;

    protected const KIND = IntLiteralKind::Octal;

    /**
     * A lone zero is a decimal number, any other legacy octal stays octal.
     */
    #[\Override]
    protected function getKind(int $value): IntLiteralKind
    {
        if ($value === 0) {
            return IntLiteralKind::Decimal;
        }

        return parent::getKind($value);
    }
}
