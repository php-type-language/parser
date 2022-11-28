<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type\TypeInterface;

final class TypeInstantiator implements TypeInstantiatorInterface
{
    /**
     * @var array<class-string, bool>
     */
    private array $constructors = [];

    /**
     * @param class-string<TypeInterface> $type
     * @param array $args
     *
     * @return TypeInterface
     * @throws \ReflectionException
     */
    public function new(string $type, array $args = []): TypeInterface
    {
        if (\array_key_exists($type, $this->constructors)) {
            if ($this->constructors[$type]) {
                return (new \ReflectionClass($type))->newInstanceArgs($args);
            }

            return new $type();
        }

        $reflection = new \ReflectionClass($type);
        $this->constructors[$type] = $reflection->hasMethod('__construct');

        if ($this->constructors[$type]) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs($args);
    }
}
