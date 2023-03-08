<?php

declare(strict_types=1);

namespace Hyper\Type\DSL;

use Hyper\Type\DSL\Exception\RegistrationException;
use Hyper\Type\DSL\Runtime\TypeBuilder;
use Hyper\Type\Repository\MutableRepositoryInterface;
use Hyper\Type\Repository\Repository as TypesRepository;
use Hyper\Type\TypeInterface;
use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Parser\ParserInterface;

final class Repository implements MutableRepositoryInterface
{
    /**
     * @var ParserInterface
     */
    private readonly ParserInterface $parser;

    /**
     * @var TypeBuilder
     */
    private readonly TypeBuilder $builder;

    /**
     * @var array<non-empty-string, TypeInterface>
     */
    private array $types = [];

    public function __construct(
        private readonly TypesRepository $parent = new TypesRepository()
    ) {
        $this->parser = new Parser();
        $this->builder = new TypeBuilder($this->parent);
    }

    private function assertValidAlias(string $alias): void
    {
        $alias = \strtolower($alias);

        if (\in_array($alias, ['true', 'false'], true)) {
            throw RegistrationException::fromReservedLiteral($alias, 'Boolean');
        }

        if ($alias === 'null') {
            throw RegistrationException::fromReservedLiteral($alias, 'Null');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add(string|TypeInterface $class, array|string $aliases): self
    {
        foreach ((array)$aliases as $alias) {
            $this->assertValidAlias($alias);
        }

        $this->parent->add($class, $aliases);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(#[Language('PHP')] string $type): TypeInterface
    {
        return $this->types[$type] ??= $this->builder->make(
            $this->parser->parse($type)
        );
    }
}
