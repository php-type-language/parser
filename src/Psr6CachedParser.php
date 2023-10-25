<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Parser\Node\Type\TypeStatement;

final class Psr6CachedParser extends CachedParser
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        ParserInterface $parent,
    ) {
        parent::__construct($parent);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function removeCacheItem(ReadableInterface $source): void
    {
        $this->cache->deleteItem($this->getCacheKey($source));
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getCachedItem(ParserInterface $parser, ReadableInterface $source): ?TypeStatement
    {
        $item = $this->cache->getItem($this->getCacheKey($source));

        if (!$item->isHit()) {
            $item->set($parser->parse($source));
        }

        return $item->get();
    }
}
