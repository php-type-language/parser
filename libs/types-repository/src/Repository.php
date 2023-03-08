<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type\Repository\Exception\InstantiationException;
use Hyper\Type\Repository\Exception\RegistrationException;
use Hyper\Type\TypeInterface;

final class Repository implements MutableRepositoryInterface
{
    /**
     * @var array<non-empty-string, class-string<TypeInterface>>
     */
    private array $aliases = [];

    /**
     * @var array<non-empty-string, TypeInterface>
     */
    private array $types = [];

    /**
     * @param iterable<class-string<TypeInterface>, non-empty-string|non-empty-array<non-empty-string>> $types
     */
    public function __construct(
        iterable $types = [],
        private readonly TypeInstantiatorInterface $instantiator = new TypeInstantiator(),
    ) {
        foreach ($types as $fqn => $aliases) {
            $this->add($fqn, $aliases);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add(string|TypeInterface $type, string|array $names): self
    {
        if ($type === '') {
            throw RegistrationException::fromEmptyName();
        }

        foreach ((array)$names as $alias) {
            if ($alias === '') {
                throw RegistrationException::fromEmptyAliasName();
            }

            if ($type instanceof TypeInterface) {
                $this->types[$alias] = $type;
                $this->aliases[$alias] = $type::class;
            } else {
                $this->aliases[$alias] = $type;
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function get(string $type, array $args = []): TypeInterface
    {
        if ($type === '') {
            throw InstantiationException::fromEmptyName();
        }

        $type = $this->aliases[$type] ?? $type;

        if ($args === []) {
            return $this->types[$type] ??= $this->new($type);
        }

        return $this->new($type, $args);
    }

    /**
     * @param non-empty-string $type
     * @param array $parameters
     *
     * @return TypeInterface
     * @throws \ReflectionException
     */
    private function new(string $type, array $parameters = []): TypeInterface
    {
        if (!\class_exists($type)) {
            throw InstantiationException::fromUndefinedType($type, \array_keys($this->aliases));
        }

        return $this->instantiator->new($type, $parameters);
    }
}
