<?php

declare(strict_types=1);

namespace Hyper\Pool;

use Hyper\Pool\Exception\PoolOverflowException;
use Hyper\Pool\Instantiator\InstantiatorInterface;
use Hyper\Pool\Reference\ReleasableReference;
use Hyper\Pool\Reference\ReferenceProviderInterface;

/**
 * @template TReference of object
 * @template TEntry of object
 *
 * @template-implements PoolInterface<TReference, TEntry>
 */
class Pool implements PoolInterface
{
    /**
     * @var \WeakMap<TReference, ReferenceProviderInterface<TEntry>>
     */
    protected \WeakMap $active;

    /**
     * @var array<TEntry>
     */
    protected array $free = [];

    /**
     * @param InstantiatorInterface<TEntry> $instantiator
     * @param int<0, max> $max
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     * @psalm-suppress PropertyTypeCoercion
     */
    public function __construct(
        protected readonly InstantiatorInterface $instantiator,
        protected readonly int $max = 0,
    ) {
        $this->active = new \WeakMap();
    }

    /**
     * @return TEntry
     */
    private function getFreeEntry(): object
    {
        if ($this->free === []) {
            // In case of max entries
            if ($this->max > 0 && \count($this->free) >= $this->max) {
                throw PoolOverflowException::fromMaxEntries($this->max);
            }

            $this->free[] = $this->instantiator->create(null);
        }

        return \array_shift($this->free);
    }

    /**
     * @param TEntry $entry
     * @return void
     */
    private function onEntryRelease(object $entry): void
    {
        $this->free[] = $this->instantiator->create($entry);
    }

    /**
     * @param TReference $reference
     * @return TEntry
     */
    private function createNewEntry(object $reference): object
    {
        $this->active[$reference] = $observer = (new ReleasableReference(
            $this->getFreeEntry(),
            $this->onEntryRelease(...),
        ));

        return $observer->getReference();
    }

    /**
     * {@inheritDoc}
     */
    public function has(object $reference): bool
    {
        return isset($this->active[$reference]);
    }

    /**
     * {@inheritDoc}
     */
    public function get(object $reference): object
    {
        if (isset($this->active[$reference])) {
            return $this->active[$reference]->getReference();
        }

        return $this->createNewEntry($reference);
    }

    /**
     * {@inheritDoc}
     */
    public function count(Status $status = null): int
    {
        return match ($status) {
            Status::FREE => \count($this->free),
            Status::ACTIVE => $this->active->count(),
            default => \count($this->free) + $this->active->count(),
        };
    }

    /**
     * @param Status|null $status
     * @return \Traversable<TReference|null, TEntry>
     */
    public function getIterator(Status $status = null): \Traversable
    {
        if ($status !== Status::FREE) {
            foreach ($this->active as $object => $ref) {
                yield $object => $ref->getReference();
            }
        }

        if ($status !== Status::ACTIVE) {
            foreach ($this->free as $connection) {
                yield null => $connection;
            }
        }
    }
}
