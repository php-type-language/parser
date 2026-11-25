<?php

declare(strict_types=1);

namespace TypeLang\Parser\Validation;

use Phplrt\Contracts\Position\PositionInterface;

/**
 * A source that is no type of its own.
 *
 * A {@see PartialCheckResult} is a failure as well: a check asks about the
 * source whole.
 *
 * @phpstan-sealed PartialCheckResult
 */
class FailureCheckResult extends CheckResult
{
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
         *
         * @var int<0, max>
         */
        public readonly int $offset,
    ) {}
}
