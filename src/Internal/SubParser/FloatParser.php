<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\FloatLiteralNode;

/**
 * Reads a float literal.
 *
 * ```
 *  1.5  -1_5.5e+3  .42  1e400
 * ```
 *
 * A float loses the exact number, so the node gets it in two more ways: the
 * mantissa and the exponent are read from the text of the literal. A number
 * out of the range of a native float is stored the same way as an integer:
 * the value is the nearest bound of the range, the mantissa and the exponent
 * are the exact number.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements SubParserInterface<FloatLiteralNode>
 */
final class FloatParser implements SubParserInterface
{
    public function parse(string $raw, int $offset = 0): FloatLiteralNode
    {
        // The separators, the gap behind the sign and the plus are not a part of the number
        $text = \ltrim(\str_replace(['_', ' ', "\t", "\n", "\r", "\v", "\f"], '', $raw), '+');

        $value = (float) $text;

        if ($value === \INF) {
            $value = \PHP_FLOAT_MAX;
        } elseif ($value === -\INF) {
            $value = -\PHP_FLOAT_MAX;
        }

        $position = \strcspn($text, 'eE');

        $mantissa = self::trimMantissa(\substr($text, 0, $position));
        $exponent = '0';

        if ($position < \strlen($text)) {
            $exponent = self::trimExponent(\substr($text, $position + 1));
        }

        \assert(\is_numeric($mantissa) && \is_numeric($exponent));

        return new FloatLiteralNode(
            value: $value,
            mantissa: $mantissa,
            exponent: $exponent,
            raw: $raw,
            offset: $offset,
        );
    }

    /**
     * Brings the mantissa to the one form: no leading zeros, the integer part
     * is always there, the point is there only with a fraction. The digits of
     * the fraction are kept as they are written.
     *
     * ```
     *  -055  // -55
     *  00.5  // 0.5
     *  .5    // 0.5
     *  1.    // 1
     *  0.10  // 0.10
     * ```
     */
    private static function trimMantissa(string $mantissa): string
    {
        $sign = $mantissa[0] === '-' ? '-' : '';
        $parts = \explode('.', \ltrim($mantissa, '-'), 2);

        $integer = \ltrim($parts[0], '0');
        $fraction = $parts[1] ?? '';

        if ($integer === '') {
            $integer = '0';
        }

        if ($fraction !== '') {
            $fraction = '.' . $fraction;
        }

        return $sign . $integer . $fraction;
    }

    /**
     * Drops the plus sign and the leading zeros: `+007` is `7`, `-00` is `0`.
     */
    private static function trimExponent(string $exponent): string
    {
        $digits = \ltrim($exponent, '+-0');

        if ($digits === '') {
            return '0';
        }

        return ($exponent[0] === '-' ? '-' : '') . $digits;
    }
}
