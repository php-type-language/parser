<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ArgumentsListNode extends Node
{
    /**
     * @param array<ArgumentNode> $list
     */
    public function __construct(
        public readonly array $list = [],
        public readonly bool $sealed = true,
    ) {
    }
}
