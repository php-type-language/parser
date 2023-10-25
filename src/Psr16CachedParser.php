<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Parser\Node\Type\TypeStatement;

final class Psr16CachedParser extends CachedParser
{
    public function __construct(
        private readonly CacheInterface $cache,
        ParserInterface $parent,
    ) {
        parent::__construct($parent);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function removeCacheItem(ReadableInterface $source): void
    {
        $this->cache->delete($this->getCacheKey($source));
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getCachedItem(ParserInterface $parser, ReadableInterface $source): ?TypeStatement
    {
        $result = $this->cache->get(
            $key = $this->getCacheKey($source),
        );

        if ($result === null) {
            $result = $parser->parse($source);

            $this->cache->set($key, $result);
        }

        return $result;
    }
}
