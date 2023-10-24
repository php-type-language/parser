<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Source\File;
use TypeLang\Parser\Node\Stmt\Type\TypeStatement;

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

    public function parse(mixed $source): ?TypeStatement
    {
        return $this->getCachedItem($this->parent, File::new($source));
    }

    public function clear(mixed $source): void
    {
        $this->removeCacheItem(File::new($source));
    }

    abstract protected function removeCacheItem(ReadableInterface $source): void;

    abstract protected function getCachedItem(ParserInterface $parser, ReadableInterface $source): ?TypeStatement;
}
