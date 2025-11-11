<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<non-empty-string>
 *
 * @phpstan-consistent-constructor
 */
class VariableLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @var non-empty-string
     */
    private readonly string $value;

    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        assert(\strlen($value) > 1, new \InvalidArgumentException(
            'Variable name length must be greater than 1',
        ));

        assert(\str_starts_with($value, '$'), new \InvalidArgumentException(
            'Variable name must start with "$" character',
        ));

        // @phpstan-ignore-next-line : Variable name gte than 2
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

    public function getValue(): string
    {
        /** @var non-empty-string */
        return $this->value;
    }
}
