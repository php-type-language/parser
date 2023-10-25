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
class IntLiteralNode extends LiteralNode
{
    /**
     * @param TValue $value
     */
    final public function __construct(
        public readonly int $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? (string)$this->value);
    }

    /**
     * @param numeric-string $value
     */
    public static function parse(string $value): self
    {
        [$isNegative, $decimal] = self::split($value);

        return new self($isNegative ? -$decimal : $decimal, $value);
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
            return [$isNegative, (int)(match ($literal[1]) {
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

        return [$isNegative, (int)$literal];
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
