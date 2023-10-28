<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @return non-empty-string
     */
    abstract public function getIdentifier(): string;
}
