<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

abstract class Visitor implements VisitorInterface
{
    #[\Override]
    public function before(): void
    {
        // NO-OP
    }

    #[\Override]
    public function enter(Node $node): ?Command
    {
        return null;
    }

    #[\Override]
    public function leave(Node $node): void
    {
        // NO-OP
    }

    #[\Override]
    public function after(): void
    {
        // NO-OP
    }
}
