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
     * @param callable(ReadableInterface):iterable<array-key, TypeStatement|DefinitionStatement> $execute
     *
     * @return iterable<array-key, TypeStatement|DefinitionStatement>
     *
     * @throws Exception\ParserExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    protected function getCachedItem(ReadableInterface $source, callable $execute): iterable
    {
        $item = $this->cache->getItem($this->getCacheKey($source));

        if (!$item->isHit()) {
            $item->set($execute($source));

            $this->cache->save($item);
        }

        /** @psalm-suppress MixedAssignment */
        $result = $item->get();

        if (\is_iterable($result)) {
            return $result;
        }

        return null;
    }
}
