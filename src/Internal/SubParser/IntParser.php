<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Parser\Internal\BaseConverter\BaseConverterInterface;
use TypeLang\Parser\Internal\SubParser\IntParser\IntNumberSign;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * A base of the parsers of integer literals, each of them reads one notation.
 *
 * A number out of the range of a native integer is stored in the node in
 * two ways: the value is the nearest bound of the range, the decimal is the
 * exact number.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements SubParserInterface<IntLiteralNode>
 */
abstract class IntParser implements SubParserInterface
{
    /**
     * The base of the digits.
     *
     * @var int<2, 36>
     */
    protected const RADIX = 10;

    /**
     * The length of the notation prefix, such as `0x`.
     *
     * @var int<0, max>
     */
    protected const PREFIX_LENGTH = 0;

    /**
     * The notation of the literals the parser reads.
     *
     * @var IntLiteralKind
     */
    protected const KIND = IntLiteralKind::Decimal;

    public function __construct(
        protected readonly BaseConverterInterface $converter,
    ) {}

    /**
     * @param non-empty-string $value
     */
    private static function getSign(string $value): IntNumberSign
    {
        return match ($value[0]) {
            '-' => IntNumberSign::Minus,
            '+' => IntNumberSign::Plus,
            default => IntNumberSign::None,
        };
    }

    /**
     * @param non-empty-string $value
     */
    private static function getDigits(string $value, IntNumberSign $sign): string
    {
        if ($sign !== IntNumberSign::None) {
            $value = \ltrim(\substr($value, 1), " \t\n\r\v\f");
        }

        return \substr($value, static::PREFIX_LENGTH);
    }

    /**
     * The notation a number of the parser is written in.
     */
    protected function getKind(int $value): IntLiteralKind
    {
        return static::KIND;
    }

    public function parse(string $raw, int $offset = 0): IntLiteralNode
    {
        // cleanup
        $number = \str_replace('_', '', $raw);

        if ($number === '') {
            throw new \UnexpectedValueException('Invalid (empty) integer literal value');
        }

        $sign = self::getSign($number);
        $digits = self::getDigits($number, $sign);

        if ($digits === '') {
            throw new \UnexpectedValueException('Invalid (empty) integer literal value');
        }

        [$value, $decimal] = $this->read($digits, $sign);

        return new IntLiteralNode(
            value: $value,
            decimal: $decimal,
            kind: $this->getKind($value),
            raw: $raw,
            offset: $offset,
        );
    }

    /**
     * Reads the digits of a number into a pair: its value, or the nearest
     * bound of the native range in case of the number is out of it, and its
     * exact decimal form.
     *
     * Only a number at the bound of the native range or beyond it needs the
     * digits to be converted.
     *
     * @param non-empty-string $digits without a sign, a prefix and separators
     * @return array{int, numeric-string}
     */
    private function read(string $digits, IntNumberSign $sign): array
    {
        // The conversion clamps a number out of the native range to its bound
        $magnitude = \intval($digits, static::RADIX);

        if ($magnitude !== \PHP_INT_MAX) {
            if ($sign === IntNumberSign::Minus) {
                $magnitude = -$magnitude;
            }

            return [$magnitude, (string) $magnitude];
        }

        $decimal = $this->converter->toDecimal($digits, static::RADIX);

        if ($sign !== IntNumberSign::Minus) {
            return [\PHP_INT_MAX, $decimal];
        }

        $value = \PHP_INT_MIN;

        if ($decimal === (string) \PHP_INT_MAX) {
            $value = -\PHP_INT_MAX;
        }

        // @phpstan-ignore-next-line : Negative numeric-string is a numeric-string too
        return [$value, '-' . $decimal];
    }
}
