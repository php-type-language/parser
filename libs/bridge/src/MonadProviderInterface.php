<?php

declare(strict_types=1);

namespace Hyper\Bridge;

/**
 * @template TInput of object
 * @template TOutput of object<TInput>
 */
interface MonadProviderInterface
{
    /**
     * Returns the generic nullable type.
     *
     * @param TInput $type
     *
     * @return TOutput
     */
    public function maybe(object $type): object;
}
