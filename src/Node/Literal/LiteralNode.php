<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @template TValue of mixed
 *
 * @template-implements LiteralNodeInterface<TValue>
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class LiteralNode extends TypeStatement implements LiteralNodeInterface
{
    public readonly string $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * Returns parsed literal value.
     */
    abstract public function getValue(): mixed;

    /**
     * Returns raw literal value string representation.
     */
    public function getRawValue(): string
    {
        return $this->raw;
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}
