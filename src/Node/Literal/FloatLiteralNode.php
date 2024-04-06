<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of float
 * @template-extends LiteralNode<TValue>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class FloatLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @param TValue $value
     */
    public function __construct(
        public readonly float $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? (string) $this->value);
    }

    /**
     * @param numeric-string $value
     *
     * @return static<float>
     * @psalm-suppress MoreSpecificImplementedParamType : Strengthening the
     *                 precondition will violate the LSP, but in this case it is
     *                 acceptable.
     */
    public static function parse(string $value): static
    {
        return new static((float) $value, $value);
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
