<?php

declare(strict_types=1);

namespace Hyper\Pool\Reference;

/**
 * @template TEntry of object
 * @template-implements ReferenceProviderInterface<TEntry>
 */
final class Reference implements ReferenceProviderInterface
{
    /**
     * @param TEntry $entry
     */
    public function __construct(
        private readonly object $entry,
    ) {
    }

    /**
     * @return TEntry
     */
    public function getReference(): object
    {
        return $this->entry;
    }
}
