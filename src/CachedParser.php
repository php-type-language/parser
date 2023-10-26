<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Source\File;
use TypeLang\Parser\Node\Type\TypeStatement;

abstract class CachedParser implements ParserInterface
{
    public function __construct(
        private readonly ParserInterface $parent,
    ) {}

    /**
     * @return non-empty-string
     */
    protected function getCacheKey(ReadableInterface $source): string
    {
        return $source->getHash();
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): iterable
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::fromSources($source);

        return $this->getCachedItem($source, fn(ReadableInterface $src): iterable => $this->parent->parse($src));
    }

    /**
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parseType(#[Language('PHP')] mixed $source): ?TypeStatement
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        $source = File::fromSources($source);

        return $this->getCachedItem($source, fn(ReadableInterface $src): ?TypeStatement => $this->parent->parseType($src));
    }

    public function clear(mixed $source): void
    {
        $this->removeCacheItem(File::new($source));
    }

    abstract protected function removeCacheItem(ReadableInterface $source): void;

    /**
     * @template TReturn of mixed
     *
     * @param callable(ReadableInterface):TReturn $execute
     *
     * @return TReturn
     */
    abstract protected function getCachedItem(ReadableInterface $source, callable $execute): mixed;
}
