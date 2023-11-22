<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

interface VisitorInterface
{
    public function before(): void;

    public function enter(Node $node): ?Command;

    public function leave(Node $node): void;

    public function after(): void;
}
