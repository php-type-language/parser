<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type\TypeInterface;

interface RepositoryInterface
{
    /**
     * @param non-empty-string $type
     * @return TypeInterface
     */
    public function get(string $type): TypeInterface;
}
