<?php

declare(strict_types=1);

namespace TypeLang\Parser\Partial;

use TypeLang\Type\Node;

/**
 * A source the grammar has read in full: the reading has stopped at the very
 * end of it.
 *
 * A {@see PartialParsedResult} is a successful one as well: a type has been
 * built either way.
 *
 * @template-covariant TResult of Node
 *
 * @phpstan-sealed PartialParsedResult
 */
class SuccessfulParsedResult extends ParsedResult
{
    public function __construct(
        /**
         * The type the source is read into.
         *
         * @var TResult
         */
        public readonly Node $type,
        /**
         * The offset the reading has stopped at.
         *
         * @var int<0, max>
         */
        public readonly int $offset,
    ) {}
}
