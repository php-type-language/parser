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
     * Returns {@see true} in case of name must be prefixed
     * by leading namespace separator.
     */
    public function isPrefixedByLeadingBackslash(): bool
    {
        return !$this->isSimple()
            || !$this->isBuiltin()
            || !$this->isSpecial();
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        if ($this->isPrefixedByLeadingBackslash()) {
            return '\\' . parent::toString();
        }

        return parent::toString();
    }
}
