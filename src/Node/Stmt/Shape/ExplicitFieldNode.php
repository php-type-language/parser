<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @deprecated Please use {@see getHashString()} instead
     */
    public function getKey(): int|string
    {
        return $this->getHashString();
    }

    /**
     * @return non-empty-string
     */
    abstract public function getHashString(): string;
}
