<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class NullLiteralNode extends LiteralNode
{
    public function __construct(string $raw = null)
    {
        parent::__construct($raw ?? 'null');
    }

    public function getValue(): null
    {
        return null;
    }
}
