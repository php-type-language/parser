<?php

declare(strict_types=1);

namespace Hyper\Pool\Reference;

/**
 * @template TEntry of object
 */
interface ReferenceProviderInterface
{
    /**
     * @return TEntry
     */
    public function getReference(): object;
}
