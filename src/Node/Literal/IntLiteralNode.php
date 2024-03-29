<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of int
 * @template-extends LiteralNode<TValue>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class IntLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @param TValue $value
     */
    public function __construct(
        public readonly int $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? (string) $this->value);
    }

    /**
     * @param numeric-string $value
     *
     * @return static<int>
     * @psalm-suppress MoreSpecificImplementedParamType : Strengthening the
     *                 precondition will violate the LSP, but in this case it is
     *                 acceptable.
     */
    public static function parse(string $value): static
    {
        [$isNegative, $decimal] = self::split($value);

        return new static($isNegative ? -$decimal : $decimal, $value);
    }

    /**
     * @param numeric-string $literal
     *
     * @return array{bool, int}
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($isNegative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            return [$isNegative, (int) (match ($literal[1]) {
                // hexadecimal
                'x', 'X' => \hexdec(\substr($literal, 2)),
                // binary
                'b', 'B' => \bindec(\substr($literal, 2)),
                // octal
                'o', 'O' => \octdec(\substr($literal, 2)),
                // octal (legacy)
                default => \octdec($literal),
            })];
        }

        return [$isNegative, (int) $literal];
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => LiteralKind::INT_KIND,
        ];
    }
}
