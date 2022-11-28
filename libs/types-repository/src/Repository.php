<?php

declare(strict_types=1);

namespace Hyper\Type\Repository;

use Hyper\Type;
use Hyper\Type\Repository\Exception\InstantiationException;
use Hyper\Type\Repository\Exception\RegistrationException;
use Hyper\Type\TypeInterface;

final class Repository implements MutableRepositoryInterface
{
    /**
     * @var non-empty-array<class-string<TypeInterface>, non-empty-string|non-empty-list<non-empty-string>>
     */
    private const DEFAULT_TYPES = [
        // Generic Types
        Type\NullableType::class => ['optional'],

        // Scalar Types
        Type\BoolType::class     => 'bool',
        Type\IntType::class      => 'int',
        Type\FloatType::class    => 'float',
        Type\StringType::class   => 'string',
        Type\BinaryType::class   => 'binary',
        Type\BigIntType::class   => 'bigint',

        // Extra Common Types
        Type\AnyType::class      => 'any',
        Type\EnumType::class     => 'enum',
        Type\JsonType::class     => 'json',
    ];

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

        if ($types === []) {
            $this->bootDefaultTypes();
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
            } else {
                $this->aliases[$alias] = $type;
            }
        }

        return $this;
    }

    /**
     * @return void
     */
    private function bootDefaultTypes(): void
    {
        foreach (self::DEFAULT_TYPES as $class => $aliases) {
            $this->add($class, $aliases);
        }
    }

    /**
     * {@inheritDoc}
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
     * @param array $args
     * @param array $params
     *
     * @return TypeInterface
     * @throws \ReflectionException
     */
    private function new(string $type, array $args = []): TypeInterface
    {
        if (!\class_exists($type)) {
            throw InstantiationException::fromUndefinedType($type, \array_keys($this->aliases));
        }

        if (!\is_subclass_of($type, TypeInterface::class)) {
            throw InstantiationException::fromNonType($type, \array_keys($this->aliases));
        }

        return $this->instantiator->new($type, $args);
    }
}
