<?php

declare(strict_types=1);

namespace TypeLang\Parser\Validation;

use Phplrt\Contracts\Position\PositionInterface;

/**
 * A source that is no type of its own.
 *
 * Note that a {@see PartialCheckResult} is a failure as well, since a check
 * asks about the source whole.
 *
 * @phpstan-sealed PartialCheckResult
 */
class FailureCheckResult extends CheckResult
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
