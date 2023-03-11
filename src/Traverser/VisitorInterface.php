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
     * @return ?Command
     */
    public function enter(Node $node): ?Command;

    /**
     *
     * @return void
     */
    public function leave(Node $node): void;

    /**
     * @return void
     */
    public function after(): void;
}
