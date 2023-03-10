<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class NullLiteralStmt extends LiteralStmt
{
    public readonly string $raw;

    public function __construct(string $raw = null)
    {
        $this->raw = $raw ?? 'null';
    }

    public function getValue(): mixed
    {
        return null;
    }
}
