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
    private array $types = [];

    public function __construct(
        TypeInterface $first,
        TypeInterface $second,
        TypeInterface ...$other,
    ) {
        $this->types = $this->unwrap($first, $second, ...$other);
    }

    /**
     * @return array<TypeInterface>
     */
    private function unwrap(TypeInterface ...$types): array
    {
        $result = [];

        foreach ($types as $type) {
            if ($type instanceof static) {
                \array_push($result, ...$type->types);
            } else {
                $result[] = $type;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }
}
