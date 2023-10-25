<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

interface NodeInterface
{
    /**
     * Returns token offset defined in the source code.
     *
     * @return int<0, max>
     */
    public function getOffset(): int;
}
