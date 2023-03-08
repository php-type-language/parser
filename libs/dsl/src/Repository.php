<?php

declare(strict_types=1);

namespace Hyper\DSL;

use Hyper\Bridge\FactoryInterface;
use Hyper\Parser\Node\Stmt\Statement;
use Hyper\Parser\Parser;
use Hyper\Repository\MutableRepositoryInterface;
use Hyper\Repository\Repository as TypesRepository;
use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Parser\ParserInterface;

/**
 * @template TType of object
 *
 * @template-implements MutableRepositoryInterface<TType>
 * @template-implements \IteratorAggregate<array-key, class-string<TType>>
 */
final class Repository implements MutableRepositoryInterface, \IteratorAggregate
{
    private readonly TypeBuilder $builder;

    private readonly TypesRepository $parent;

    /**
     * @var array<non-empty-string, TType>
     */
    private array $types = [];

    public function __construct(
        FactoryInterface $bridge,
        TypesRepository $parent = null,
        private readonly ParserInterface $parser = new Parser(),
    ) {
        $this->parent = $parent ?? new TypesRepository($bridge);
        $this->builder = new TypeBuilder($this->parent, $bridge);
    }

    /**
     * {@inheritDoc}
     */
    public function add(string $type, string $name, string ...$aliases): void
    {
        $this->parent->add($type, $name, ...$aliases);
    }

    /**
     * {@inheritDoc}
     */
    public function singleton(object $type, string $name, string ...$aliases): void
    {
        $this->parent->singleton($type, $name, ...$aliases);
    }

    /**
     * {@inheritDoc}
     */
    public function load(iterable $types): void
    {
        $this->parent->load($types);
    }

    /**
     * {@inheritDoc}
     */
    public function get(#[Language('PHP')] string $type): object
    {
        return $this->types[$type] ??= $this->builder->make($this->parse($type));
    }

    private function parse(string $expr): Statement
    {
        foreach ($this->parser->parse($expr) as $stmt) {
            return $stmt;
        }

        throw new \InvalidArgumentException('Empty expression');
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return $this->parent->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->parent->count();
    }
}
