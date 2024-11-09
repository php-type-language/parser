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
    public function __construct(
        public readonly int $value,
        ?string $raw = null,
    ) {
        parent::__construct($raw ?? (string) $this->value);
    }

    public static function parse(string $value): static
    {
        [$negative, $numeric] = self::split($value);

        if ($negative) {
            if ((string) \PHP_INT_MIN === '-' . $numeric) {
                return new static(\PHP_INT_MIN, $value);
            }

            return new static((int) ('-' . $numeric), $value);
        }

        return new static((int) $numeric, $value);
    }

    /**
     * @return array{bool, numeric-string}
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($isNegative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            return [$isNegative, match ($literal[1]) {
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

        return [$isNegative, $literal];
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
