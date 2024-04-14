<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<float>
 *
 * @psalm-consistent-constructor
 * @phpstan-consistent-constructor
 */
class FloatLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    public function __construct(
        public readonly float $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? (string) $this->value);
    }

    public static function parse(string $value): static
    {
        if (!\is_numeric($value)) {
            return new static(0.0, $value);
        }

        return new static((float) $value, $value);
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
