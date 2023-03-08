<?php

declare(strict_types=1);

namespace Hyper\Type\Variadic;

use Hyper\Type\VariadicTypeInterface;
use Hyper\Type\TypeInterface;

abstract class VariadicType implements VariadicTypeInterface
{
    /**
     * @var non-empty-list<TypeInterface>
     */
    private readonly array $types;

    public function __construct(
        TypeInterface $first,
        TypeInterface $second,
        TypeInterface ...$other,
    ) {
        $this->types = [$first, $second, ...$other];
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }
}
