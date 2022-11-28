<?php

declare(strict_types=1);

namespace Hyper\Pool;

use Hyper\Pool\Instantiator\InstantiatorInterface;
use Hyper\Pool\Reference\Reference;
use Hyper\Pool\Reference\ReferenceProviderInterface;
use Hyper\Pool\Reference\ReleasableReference;

/**
 * @template TReference of object
 * @template TEntry of object
 *
 * @template-extends Pool<TReference, TEntry>
 */
class PrimaryPool extends Pool implements PrimaryPoolInterface
{
    /**
     * @return object
     */
    protected function getReferenceForMasterEntry(): object
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(?object $reference = null): object
    {
        // Use constant reference as connection relation when the master object
        // of the pool is required.
        if ($reference === null) {
            $reference = $this->getReferenceForMasterEntry();
        }

        return parent::get($reference);
    }
}
