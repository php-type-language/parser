<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class FullQualifiedName extends Name
{
    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return '\\' . parent::toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
