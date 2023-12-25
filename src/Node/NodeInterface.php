<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

interface NodeInterface extends SerializableInterface
{
    /**
     * Returns token offset defined in the source code.
     *
     * @return int<0, max>
     */
    public function getOffset(): int;
}
