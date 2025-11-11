<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * Returns pretty-printed key value
     */
    abstract public function getKey(): int|string;
}
