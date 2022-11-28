<?php

declare(strict_types=1);

namespace Hyper\Pool;

/**
 * Implementing a {@see PoolInterface} that provides obtaining capabilities for
 * the primary object when passing {@see null} context reference.
 *
 * @template TReference of object
 * @template TEntry of object
 *
 * @template-extends PoolInterface<TReference, TEntry>
 */
interface PrimaryPoolInterface extends PoolInterface
{
    /**
     * @param TReference|null $reference
     * @return TEntry
     */
    public function get(?object $reference = null): object;
}
