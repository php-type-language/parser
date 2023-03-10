<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\ReadableInterface;

trait InteractWithDocBlocks
{
    /**
     * @param list<non-empty-string> $tags
     *
     * @return iterable<non-empty-string, PositionInterface>|list<\Throwable>
     */
    protected static function getDocTypesFromSource(ReadableInterface $source, array $tags): iterable
    {
        $reader = new DocBlockReader();

        return $reader->readOrFail($source, $tags);
    }

    /**
     * @template TSource of ReadableInterface
     *
     * @param list<TSource> $sources
     * @param list<non-empty-string> $tags
     *
     * @return iterable<non-empty-string, array{TSource, PositionInterface}>
     */
    protected static function getDocTypesFromSources(iterable $sources, array $tags): iterable
    {
        $reader = new DocBlockReader();

        foreach ($sources as $source) {
            foreach ($reader->read($source, $tags) as $type => $position) {
                yield $type => [$source, $position];
            }
        }
    }

    /**
     * @template TSource of ReadableInterface
     *
     * @param list<TSource> $sources
     * @param list<non-empty-string> $tags
     *
     * @return iterable<non-empty-string, array{TSource, PositionInterface}>
     */
    protected static function fetchDocTypesFromSources(iterable $sources, array $tags): iterable
    {
        $reader = new DocBlockReader();

        foreach ($sources as $source) {
            try {
                foreach ($reader->read($source, $tags) as $type => $position) {
                    yield $type => [$source, $position];
                }
            } catch (\Throwable $e) {
                \fwrite(\STDERR, (string)$e->getMessage() . ' in ' . $source->getPathname());
            }
        }
    }
}
