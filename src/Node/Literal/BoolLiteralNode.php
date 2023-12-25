<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of bool
 * @template-extends LiteralNode<TValue>
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class BoolLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @param TValue $value
     */
    public function __construct(
        public readonly bool $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? ($value ? 'true' : 'false'));
    }

    /**
     * @param non-empty-string $value
     *
     * @return static<bool>
     * @psalm-suppress MoreSpecificImplementedParamType : Strengthening the
     *                 precondition will violate the LSP, but in this case it is
     *                 acceptable.
     */
    public static function parse(string $value): static
    {
        return new static(\strtolower($value) === 'true', $value);
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'kind' => LiteralKind::BOOL_KIND,
        ];
    }
}
