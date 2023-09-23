<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class Name extends Node
{
    /**
     * @var non-empty-string
     */
    public readonly string $name;

    /**
     * @param non-empty-list<non-empty-string> $parts
     */
    final public function __construct(public readonly array $parts)
    {
        assert($this->parts !== [], new \InvalidArgumentException(
            'Name parts count can not be empty',
        ));

        $this->name = \implode('\\', $this->parts);
    }

    public function isSimple(): bool
    {
        return \count($this->parts) === 1;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
