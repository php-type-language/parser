<?php

declare(strict_types=1);

namespace Hyper\Repository;

use Hyper\Repository\Exception\RegistrationException;

/**
 * @template TType of object
 *
 * @template-extends RepositoryInterface<TType>
 */
interface MutableRepositoryInterface extends RepositoryInterface
{
    /**
     * @param iterable<non-empty-string, class-string<TType>|TType> $types
     */
    public function load(iterable $types): void;

    /**
     * @param class-string<TType> $type
     * @param non-empty-string $name
     * @param non-empty-string ...$aliases
     *
     * @throws RegistrationException
     */
    public function add(string $type, string $name, string ...$aliases): void;

    /**
     * @param TType $type
     * @param non-empty-string $name
     * @param non-empty-string ...$aliases
     *
     * @throws RegistrationException
     */
    public function singleton(object $type, string $name, string ...$aliases): void;
}
