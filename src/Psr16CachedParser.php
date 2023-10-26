<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Definition\DefinitionStatement;
use TypeLang\Parser\Node\Type\TypeStatement;

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
     * @param callable(ReadableInterface):iterable<array-key, TypeStatement|DefinitionStatement> $execute
     *
     * @return iterable<array-key, TypeStatement|DefinitionStatement>
     *
     * @throws ParserExceptionInterface
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    protected function getCachedItem(ReadableInterface $source, callable $execute): iterable
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
