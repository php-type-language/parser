<?php

declare(strict_types=1);

namespace Hyper\Pool\Instantiator;

/**
 * @template TEntry of object
 */
interface InstantiatorInterface
{
    /**
     * Returns a new object that is put into the pool.
     *
     * The second argument ({@see $previous}) contains the previous value
     * argument in case the object is returned back to the pool of unused
     * values.
     *
     * For example:
     * ```
     *  $instantiator = new class implements InstantiatorInterface
     *  {
     *      public function create(?object $previous): \PDO
     *      {
     *          // In the case that the connection does not contain open
     *          // transactions, then we use the existing connection.
     *          if ($previous?->inTransaction() === false) {
     *              return $previous;
     *          }
     *
     *          // Create a new connection if it is new or there are
     *          // pending transactions.
     *          return new \PDO('sqlite::memory:');
     *      }
     *  };
     * ```
     *
     * @param TEntry|null $previous
     * @return TEntry
     */
    public function create(?object $previous): object;
}
