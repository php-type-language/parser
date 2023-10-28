<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\ClassLike;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

final class ClassExtendsNode extends Node
{
    public function __construct(
        public readonly NamedTypeNode $type,
    ) {}
}
