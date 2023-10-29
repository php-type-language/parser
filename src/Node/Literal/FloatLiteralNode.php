<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of float
 * @template-extends LiteralNode<TValue>
 * @template-implements ParsableLiteralNodeInterface<TValue, numeric-string>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class FloatLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @param TValue $value
     */
    final public function __construct(
        public readonly float $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? (string)$this->value);
    }

    public static function parse(string $value): self
    {
        return new self((float)$value, $value);
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
