<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class Node implements NodeInterface
{
    /**
     * @var int<0, max>
     */
    public int $offset = 0;

    public function getOffset(): int
    {
        return $this->offset;
    }
}
