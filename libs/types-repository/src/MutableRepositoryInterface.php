<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type\TypeInterface;

interface MutableRepositoryInterface extends RepositoryInterface
{
    /**
     * @param class-string<TypeInterface>|TypeInterface $type
     * @param non-empty-string|non-empty-array<non-empty-string> $names
     *
     * @return $this
     */
    public function add(string|TypeInterface $type, string|array $names): self;
}
