<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\VisitorInterface;

interface MutableTraverserInterface extends TraverserInterface
{
    /**
     * @param VisitorInterface $visitor
     *
     * @return $this
     */
    public function append(VisitorInterface $visitor): self;

    /**
     * @param VisitorInterface $visitor
     *
     * @return $this
     */
    public function prepend(VisitorInterface $visitor): self;
}
