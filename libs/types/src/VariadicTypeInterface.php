<?php

declare(strict_types=1);

namespace Hyper\Type;

interface VariadicTypeInterface extends TypeInterface
{
    /**
     * @return non-empty-list<TypeInterface>
     */
    public function getTypes(): iterable;
}
