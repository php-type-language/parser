<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class Psr6CachedParser extends CachedParser
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        ParserInterface $parent = new Parser(),
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
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    protected function getCachedItem(ParserInterface $parser, ReadableInterface $source): ?TypeStatement
    {
        $key = $this->getCacheKey($source);
        $item = $this->cache->getItem($key);

        /** @psalm-suppress MixedAssignment */
        $result = $item->get();

        if (!\is_iterable($result)) {
            $item->set($result = $parser->parse($source));

            $this->cache->save($item);
        }

        return $result;
    }
}
