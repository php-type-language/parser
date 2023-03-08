<?php

declare(strict_types=1);

namespace Hyper\Repository;

use Hyper\Repository\Exception\InstantiationException;

/**
 * @template TType of object
 *
 * @template-extends \Traversable<array-key, class-string<TType>>
 */
interface RepositoryInterface extends \Countable, \Traversable
{
    /**
     * @param non-empty-string|class-string<TType> $type
     * @return TType
     * @psalm-return ($type is class-string<TType> ? TType : object)
     *
     * @throws InstantiationException
     */
    public function get(string $type): object;
}
