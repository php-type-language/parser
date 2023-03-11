<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\VisitorInterface;

interface MutableTraverserInterface extends TraverserInterface
{
    /**
     * @return $this
     */
    public function append(VisitorInterface $visitor): self;

    /**
     * @return $this
     */
    public function prepend(VisitorInterface $visitor): self;
}
