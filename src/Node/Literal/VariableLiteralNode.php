<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of non-empty-string
 * @template-extends LiteralNode<TValue>
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

    /**
     * @param non-empty-string $value
     *
     * @return static<non-empty-string>
     * @psalm-suppress MoreSpecificImplementedParamType : Strengthening the
     *                 precondition will violate the LSP, but in this case it is
     *                 acceptable.
     */
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

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => LiteralKind::VARIABLE_KIND,
        ];
    }
}
