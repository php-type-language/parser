<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ParametersListNode extends Node
{
    /**
     * @param array<ParameterNode> $list
     */
    public function __construct(
        public readonly array $list = [],
    ) {
    }
}
