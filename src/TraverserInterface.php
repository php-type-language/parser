<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Traverser\VisitorInterface;

interface TraverserInterface
{
    public function with(VisitorInterface $visitor, bool $prepend = false): self;

    /**
     * @param iterable<array-key, Node> $nodes
     */
    public function traverse(iterable $nodes): void;
}
