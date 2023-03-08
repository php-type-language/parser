<?php

declare(strict_types=1);

namespace Hyper\Hydrator;

use Ds\Map;
use Hyper\Hydrator\Metadata\ClassLikeMetadata;
use Hyper\Hydrator\Metadata\ClassMetadata;
use Hyper\Type\Repository\Repository;
use Hyper\Type\Repository\RepositoryInterface;

final class Hydrator implements HydratorInterface
{
    /**
     * @var Map<non-empty-string, ClassMetadata>
     */
    public readonly Map $classes;

    public function __construct(
        private readonly RepositoryInterface $types = new Repository(),
    ) {
    }

    public function hydrate(string $class, array $data): object
    {

    }
}
