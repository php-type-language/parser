<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\VisitorInterface;
use TypeLang\Type\Node;

interface TraverserInterface
{
    public function with(VisitorInterface $visitor, bool $prepend = false): self;

    /**
     * @param iterable<array-key, Node> $nodes
     */
    public function traverse(iterable $nodes): void;
}
