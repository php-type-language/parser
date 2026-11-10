<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Snapshot;

/**
 * A file of sources and of what each of them is read into:
 *
 * ```
 * # A comment
 * >>> Foo<int>
 * <<< accepted NamedTypeNode@0(...)
 * ```
 *
 * A source is written in the form of {@see \addcslashes()}, so it takes one
 * line whatever characters it is made of.
 */
final class SnapshotFile
{
    private const SOURCE_PREFIX = '>>>';

    private const OUTCOME_PREFIX = '<<<';

    /**
     * The characters of a source written down as escape sequences.
     */
    private const ESCAPED_CHARS = "\0..\37\\\177..\377";

    /**
     * @param non-empty-string $pathname
     */
    public function __construct(
        public readonly string $pathname,
    ) {}

    /**
     * Returns the sources with the recorded outcomes, indexed by the numbers
     * of the lines the sources are written at.
     *
     * @return array<int<1, max>, array{string, string}>
     * @throws \LogicException
     */
    public function getCases(): array
    {
        $result = [];
        $pending = null;

        foreach ($this->readLines() as $index => $line) {
            if ($pending !== null) {
                if (!\str_starts_with($line, self::OUTCOME_PREFIX)) {
                    throw new \LogicException(\sprintf(
                        'The source at %s:%d has no recorded outcome, the snapshots have to be updated',
                        $this->pathname,
                        $pending,
                    ));
                }

                $result[$pending][1] = \substr($line, \strlen(self::OUTCOME_PREFIX) + 1);
                $pending = null;

                continue;
            }

            if (\str_starts_with($line, self::SOURCE_PREFIX)) {
                $pending = $index + 1;
                $result[$pending] = [self::decode($line), ''];
            }
        }

        if ($pending !== null) {
            throw new \LogicException(\sprintf(
                'The source at %s:%d has no recorded outcome, the snapshots have to be updated',
                $this->pathname,
                $pending,
            ));
        }

        return $result;
    }

    /**
     * Records the outcome of every source of the file anew, the sources and
     * the comments are kept as they are.
     *
     * @param \Closure(string): string $describe
     */
    public function update(\Closure $describe): void
    {
        $result = [];

        foreach ($this->readLines() as $line) {
            if (\str_starts_with($line, self::OUTCOME_PREFIX)) {
                continue;
            }

            $result[] = $line;

            if (\str_starts_with($line, self::SOURCE_PREFIX)) {
                $result[] = self::OUTCOME_PREFIX . ' ' . $describe(self::decode($line));
            }
        }

        \file_put_contents($this->pathname, \implode("\n", $result) . "\n");
    }

    /**
     * Returns the line a source is written down by.
     *
     * @return non-empty-string
     */
    public static function encode(string $source): string
    {
        $encoded = \addcslashes($source, self::ESCAPED_CHARS);

        // The space at the end of a line does not survive an editor
        if (\str_ends_with($encoded, ' ')) {
            $encoded = \substr($encoded, 0, -1) . '\\040';
        }

        return \rtrim(self::SOURCE_PREFIX . ' ' . $encoded);
    }

    private static function decode(string $line): string
    {
        return \stripcslashes(\substr($line, \strlen(self::SOURCE_PREFIX) + 1));
    }

    /**
     * @return list<string>
     */
    private function readLines(): array
    {
        $content = \file_get_contents($this->pathname);

        if ($content === false) {
            throw new \RuntimeException(\sprintf('The snapshot %s is not readable', $this->pathname));
        }

        $lines = \preg_split('/\R/', \rtrim($content));

        \assert($lines !== false);

        return $lines;
    }
}
