<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Parser\Node\Definition\DefinitionStatement;
use TypeLang\Parser\Node\Type\TypeStatement;

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
    protected function getCachedItem(ReadableInterface $source, callable $execute): mixed
    {
        $key = $this->getCacheKey($source);
        $item = $this->cache->getItem($key);

        /** @psalm-suppress MixedAssignment */
        $result = $item->get();

        if (!\is_iterable($result)) {
            $item->set($result = $execute($source));

            $this->cache->save($item);
        }

        return $result;
    }
}
