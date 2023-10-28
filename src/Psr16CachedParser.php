<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Parser\Node\Stmt\DefinitionStatement;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final class Psr16CachedParser extends CachedParser
{
    public function __construct(
        private readonly CacheInterface $cache,
        ParserInterface $parent = new Parser(),
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
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    protected function getCachedItem(ReadableInterface $source, callable $execute): mixed
    {
        $key = $this->getCacheKey($source);

        /** @psalm-suppress MixedAssignment */
        $result = $this->cache->get($key);

        if (!\is_iterable($result)) {
            $this->cache->set($key, $result = $execute($source));
        }

        return $result;
    }
}
