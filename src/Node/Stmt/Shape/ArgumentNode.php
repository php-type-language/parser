<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Statement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ArgumentNode extends Node
{
    public function __construct(
        public readonly Statement $value,
        public readonly ?StringLiteralNode $name = null,
        public readonly bool $optional = false,
    ) {
    }
}
