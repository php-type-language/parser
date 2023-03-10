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
     * @var bool
     */
    public readonly bool $isSimple;

    /**
     * @param non-empty-array<string> $parts
     */
    final public function __construct(public readonly array $parts)
    {
        $this->name = \implode('\\', $this->parts);
        $this->isSimple = \count($this->parts) === 1;
    }
}
