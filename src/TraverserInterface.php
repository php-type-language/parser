<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Traverser\VisitorInterface;

interface TraverserInterface
{
    /**
     * @psalm-immutable
     */
    public function with(VisitorInterface $visitor, bool $prepend = false): self;

    /**
     * @param list<Node> $nodes
     *
     * @return void
     */
    public function traverse(iterable $nodes): void;
}
