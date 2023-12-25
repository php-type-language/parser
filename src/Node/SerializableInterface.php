<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

interface SerializableInterface extends \JsonSerializable
{
    /**
     * Get the instance as an array.
     */
    public function toArray(): array;
}
