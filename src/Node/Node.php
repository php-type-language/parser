<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

abstract class Node implements NodeInterface
{
    /**
     * @var int<0, max>
     */
    public int $offset = 0;

    /**
     * @var int<0, max>
     */
    public int $offsetTo = 0;

    public function getOffset(): int
    {
        return $this->offset;
    }
}
