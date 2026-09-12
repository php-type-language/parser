<?php

declare(strict_types=1);

namespace TypeLang\Parser\Partial;

use Phplrt\Contracts\Position\PositionInterface;

/**
 * A source that opens no type at all, so nothing has been built of it.
 */
final class FailureParsedResult extends ParsedResult
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * What stands in the way of the reading.
         */
        public readonly string $message,
        /**
         * The line and the column the reading has stopped at.
         */
        public readonly PositionInterface $position,
        /**
         * The offset the reading has stopped at, which is the one the
         * {@see $position} above is made of.
         */
        public readonly int $offset,
    ) {}
}
