<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<int>
 *
 * @psalm-consistent-constructor
 *
 * @phpstan-consistent-constructor
 */
class IntLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @var numeric-string
     */
    public readonly string $decimal;

    /**
     * @param numeric-string|null $decimal
     */
    public function __construct(
        public readonly int $value,
        ?string $raw = null,
        ?string $decimal = null,
    ) {
        $this->decimal = $decimal ?? (string) $this->value;

        parent::__construct($raw ?? (string) $this->value);
    }

    public static function parse(string $value): static
    {
        [$negative, $decimal] = self::split($value);

        $converted = '-' . $decimal;

        if ($negative) {
            if ((string) \PHP_INT_MIN === '-' . $decimal) {
                return new static(\PHP_INT_MIN, $value, $converted);
            }

            return new static((int) ('-' . $decimal), $value, $converted);
        }

        return new static((int) $decimal, $value, $converted);
    }

    /**
     * @return array{bool, numeric-string}
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($negative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            /** @var array{bool, numeric-string} */
            return [$negative, match ($literal[1]) {
                // hexadecimal
                'x', 'X' => \base_convert(\substr($literal, 2), 16, 10),
                // binary
                'b', 'B' => \base_convert(\substr($literal, 2), 2, 10),
                // octal
                'o', 'O' => \base_convert(\substr($literal, 2), 8, 10),
                // octal (legacy)
                default => \base_convert($literal, 8, 10),
            }];
        }

        /** @var array{bool, numeric-string} */
        return [$negative, $literal];
    }

    /**
     * @return numeric-string
     */
    public function getValueAsDecimalString(): string
    {
        return $this->decimal;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
