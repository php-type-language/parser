<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<bool>
 *
 * @psalm-consistent-constructor
 * @phpstan-consistent-constructor
 */
class BoolLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    public function __construct(
        public readonly bool $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? ($value ? 'true' : 'false'));
    }

    public static function parse(string $value): static
    {
        return new static(\strtolower($value) === 'true', $value);
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
