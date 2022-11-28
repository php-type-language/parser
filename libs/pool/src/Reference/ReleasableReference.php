<?php

declare(strict_types=1);

namespace Hyper\Pool\Reference;

/**
 * @template TEntry of object
 * @template-implements ReferenceProviderInterface<TEntry>
 */
final class ReleasableReference implements ReferenceProviderInterface
{
    /**
     * @var \Closure(TEntry):void
     */
    private readonly \Closure $listener;

    /**
     * @param TEntry $entry
     * @param \Closure(TEntry):void $onRelease
     */
    public function __construct(
        private readonly object $entry,
        \Closure $onRelease,
    ) {
        $this->listener = $onRelease;
    }

    /**
     * @return TEntry
     */
    public function getReference(): object
    {
        return $this->entry;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        ($this->listener)($this->entry);
    }
}
