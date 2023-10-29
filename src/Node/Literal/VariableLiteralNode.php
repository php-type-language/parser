<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of non-empty-string
 * @template-extends LiteralNode<TValue>
 * @template-implements ParsableLiteralNodeInterface<TValue, non-empty-string>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class VariableLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @var TValue
     */
    private readonly string $value;

    /**
     * @param TValue $value
     */
    final public function __construct(string $value)
    {
        assert(\str_starts_with($value, '$'), new \InvalidArgumentException(
            'Variable name must start with "$" character',
        ));

        assert(\strlen($value) >= 1, new \InvalidArgumentException(
            'Variable name length must be greater than 0',
        ));

        /** @psalm-suppress PropertyTypeCoercion : Applied value is non-empty-string too */
        $this->value = \substr($value, 1);

        parent::__construct($value);
    }

    public static function parse(string $value): static
    {
        if (!\str_starts_with($value, '$')) {
            $value = '$' . $value;
        }

        return new static($value);
    }

    /**
     * @return TValue
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
