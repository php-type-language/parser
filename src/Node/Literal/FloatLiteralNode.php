<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template TValue of float
 * @template-extends LiteralNode<TValue>
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class FloatLiteralNode extends LiteralNode
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

    /**
     * @param numeric-string $value
     */
    public static function parse(string $value): self
    {
        return new self((float)$value, $value);
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
