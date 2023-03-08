<?php

declare(strict_types=1);

namespace Hyper\Bridge;

/**
 * @template TInput of object
 * @template TOutput of object<TInput>
 */
interface LogicalProviderInterface
{
    /**
     * Returns the variadic logical "OR" type.
     *
     * @param TInput $a
     * @param TInput $b
     * @param TInput ...$other
     *
     * @return TOutput
     */
    public function or(object $a, object $b, object ...$other): object;

    /**
     * Returns the variadic logical "AND" type.
     *
     * @param TInput $a
     * @param TInput $b
     * @param TInput ...$other
     *
     * @return TOutput
     */
    public function and(object $a, object $b, object ...$other): object;
}
