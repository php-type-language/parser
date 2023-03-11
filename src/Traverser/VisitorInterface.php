<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Parser\Node\Node;

interface VisitorInterface
{
    /**
     * @return void
     */
    public function before(): void;

    /**
     * @param Node $node
     *
     * @return ?Command
     */
    public function enter(Node $node): ?Command;

    /**
     * @param Node $node
     *
     * @return void
     */
    public function leave(Node $node): void;

    /**
     * @return void
     */
    public function after(): void;
}
