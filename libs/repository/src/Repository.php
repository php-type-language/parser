<?php

declare(strict_types=1);

namespace Hyper\Repository;

use Hyper\Bridge\Exception\InstantiatorExceptionInterface;
use Hyper\Bridge\FactoryInterface;
use Hyper\Bridge\InstantiatorInterface;
use Hyper\Repository\Exception\InstantiationException;
use Hyper\Repository\Exception\RegistrationException;

/**
 * @template TType of object
 *
 * @template-implements MutableRepositoryInterface<TType>
 * @template-implements \IteratorAggregate<array-key, class-string<TType>>
 */
final class Repository implements MutableRepositoryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, class-string<TType>>
     */
    private array $factories = [];

    /**
     * @var array<non-empty-string, TType>
     */
    private array $singletons = [];

    /**
     * @param InstantiatorInterface<TType> $bridge
     * @param iterable<non-empty-string, class-string<TType>|TType> $types
     */
    public function __construct(
        private readonly FactoryInterface $bridge,
        iterable $types = [],
    ) {
        $this->load($types);
    }

    /**
     * @param iterable<non-empty-string, class-string<TType>|TType> $types
     */
    public function load(iterable $types): void
    {
        foreach ($types as $alias => $type) {
            if (\is_string($type)) {
                $this->add($type, $alias);
            } else {
                $this->singleton($type, $alias);
            }
        }
    }

    /**
     * @psalm-taint-sink file $pathname
     * @param non-empty-string $pathname
     */
    public function loadFromDictionary(string $pathname): void
    {
        if (!\is_file($pathname)) {
            throw new \InvalidArgumentException('Dictionary "' . $pathname . '" not found');
        }

        $this->load(require $pathname);
    }

    /**
     * {@inheritDoc}
     */
    public function add(string $type, string $name, string ...$aliases): void
    {
        assert($name !== '', RegistrationException::fromEmptyName());
        $this->factories[\strtolower($name)] = $type;

        foreach ($aliases as $alias) {
            assert($alias !== '', RegistrationException::fromEmptyAliasName());
            $this->factories[\strtolower($alias)] = $type;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function singleton(object $type, string $name, string ...$aliases): void
    {
        assert($name !== '', RegistrationException::fromEmptyName());
        $this->singletons[\strtolower($name)] = $type;
        $this->factories[\strtolower($name)] = $type::class;

        foreach ($aliases as $alias) {
            assert($alias !== '', RegistrationException::fromEmptyAliasName());
            $this->singletons[\strtolower($alias)] = $type;
            $this->factories[\strtolower($alias)] = $type::class;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws InstantiatorExceptionInterface
     */
    public function get(string $type, array $params = []): object
    {
        if ($type === '') {
            throw InstantiationException::fromEmptyName();
        }

        $lower = \strtolower($type);

        // Is has been defined as singleton?
        if ($params === []) {
            return $this->singletons[$lower] ??= $this->new($this->factories[$lower] ?? $type);
        }

        return $this->new($this->factories[$lower] ?? $type, $params);
    }

    /**
     * @param class-string<TType> $type
     *
     * @return TType
     */
    private function new(string $type, array $params = []): object
    {
        if (\class_exists($type)) {
            if ($params === []) {
                return $this->bridge->reference($type);
            }

            try {
                return $this->bridge->new($type, $params);
            } catch (\Throwable $e) {
                throw new InstantiationException($e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        /** @var list<non-empty-string> $names */
        $names = \array_unique([
            ...\array_keys($this->factories),
            ...\array_keys($this->singletons),
        ]);

        throw InstantiationException::fromUndefinedType($type, $names);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->factories);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->factories);
    }
}
