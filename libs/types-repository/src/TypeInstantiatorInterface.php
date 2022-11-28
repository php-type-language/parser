<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type\TypeInterface;

interface TypeInstantiatorInterface
{
    /**
     * @param class-string<TypeInterface> $type
     * @param array $args
     *
     * @return TypeInterface
     */
    public function new(string $type, array $args = []): TypeInterface;
}
