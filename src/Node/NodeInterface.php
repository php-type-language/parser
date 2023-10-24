<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library interface, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
interface NodeInterface
{
    /**
     * Returns token offset defined in the source code.
     *
     * @return int<0, max>
     */
    public function getOffset(): int;
}
