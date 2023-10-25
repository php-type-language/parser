<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<null>
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @psalm-consistent-constructor
 */
class NullLiteralNode extends LiteralNode
{
    final public function __construct(string $raw = null)
    {
        parent::__construct($raw ?? 'null');
    }

    /**
     * @return null Note: Standalone `null` literal available since php 8.2.
     */
    public function getValue(): mixed
    {
        return null;
    }
}
