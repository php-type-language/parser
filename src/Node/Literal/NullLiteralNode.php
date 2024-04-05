<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-extends LiteralNode<null>
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

    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),
            'kind' => LiteralKind::NULL_KIND,
        ];
    }
}
