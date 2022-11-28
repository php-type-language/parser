<?php

declare(strict_types=1);

namespace Hyper\Pool;

/**
 * Implementation of a pool of objects dependent on an external context.
 *
 * If the main object is initialized, it is placed in this pool. As soon as the
 * dependent object disappears from the scope of the PHP, the main object
 * is freed and moved to the list of available ones for the next time.
 *
 * For example:
 * ```php
 * $pool = ...;
 *
 * $object1 = $pool->get($context1); // Select or create Object#1 by Context#1
 * $object2 = $pool->get($context2); // Select or create Object#2 by Context#2
 * $object2 = $pool->get($context2); // The Object#2 will be available as long
 *                                   // as the Context#2 is alive.
 *
 * unset($context2);                 // In case we destroy the Context#2 then
 *                                   // the main Object#2 is freed and available
 *                                   // to the next context
 *
 * $object2 = $pool->get($context3); // Reacquiring any object in a different
 *                                   // context will return the freed Object#2
 * ```
 *
 * A more realistic example might look like this:
 * ```php
 * $pool = new DatabaseConnectionPool(function(?Connection $prev): Connection {
 *      if ($prev === null) {
 *          return new Connection(...); // Establish new connection
 *      }
 *
 *      $prev->reset(); // For example: Cancel all database transactions
 *
 *      return $prev;
 * });
 *
 * $connection1 = $pool->get($httpRequest1);
 * $connection2 = $pool->get($httpRequest2);
 * $connection1 = $pool->get($httpRequest1);
 * ```
 *
 * @template TReference of object
 * @template TEntry of object
 *
 * @template-extends \IteratorAggregate<TReference|null, TEntry>
 */
interface PoolInterface extends \Countable, \IteratorAggregate
{
    /**
     * @param TReference $reference
     * @return TEntry
     */
    public function get(object $reference): object;

    /**
     * @param TReference $reference
     * @return bool
     */
    public function has(object $reference): bool;
}
