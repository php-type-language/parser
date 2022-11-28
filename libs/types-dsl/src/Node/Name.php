<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
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
     * @param int<0, max> $offset
     * @param non-empty-array<string> $parts
     */
    public function __construct(int $offset, public readonly array $parts)
    {
        $this->name = \implode('\\', $this->parts);
        $this->isSimple = \count($this->parts) === 1;

        parent::__construct($offset);
    }

    /**
     * @param non-empty-list<TokenInterface> $tokens
     * @return static
     */
    public static function parse(iterable $tokens): self
    {
        $offset = null;
        $parts = [];

        foreach ($tokens as $token) {
            $offset ??= $token->getOffset();
            $parts[] = $token->getValue();
        }

        return new self($offset, $parts);
    }
}
