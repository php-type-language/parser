<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceExceptionInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Node\Stmt\TypeStatement;

abstract class CachedParser implements ParserInterface
{
    public function __construct(
        private readonly ParserInterface $parent,
        private readonly SourceFactoryInterface $sources = new SourceFactory(),
    ) {}

    /**
     * @return non-empty-string
     *
     * @throws SourceExceptionInterface In case of source hash creation error.
     */
    protected function getCacheKey(ReadableInterface $source): string
    {
        return $source->getHash();
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     *
     * @throws SourceExceptionInterface In case of source creation error.
     */
    public function parse(#[Language('PHP')] mixed $source): ?TypeStatement
    {
        $instance = $this->sources->create($source);

        return $this->getCachedItem($this->parent, $instance);
    }

    /**
     * @throws SourceExceptionInterface In case of source creation error.
     */
    public function clear(mixed $source): void
    {
        $instance = $this->sources->create($source);

        $this->removeCacheItem($instance);
    }

    abstract protected function removeCacheItem(ReadableInterface $source): void;

    abstract protected function getCachedItem(ParserInterface $parser, ReadableInterface $source): ?TypeStatement;
}
