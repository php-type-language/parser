<?php

declare(strict_types=1);

namespace TypeLang\Parser\Partial;

use TypeLang\Type\TypeNode;

/**
 * A source the grammar has read in full, so the reading has stopped at the
 * very end of it.
 *
 * Note that a {@see PartialParsedResult} is a successful one as well, since
 * a type has been built either way.
 *
 * @phpstan-sealed PartialParsedResult
 */
class SuccessfulParsedResult extends ParsedResult
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The type the source is read into.
         */
        public readonly TypeNode $type,
        /**
         * The offset the reading has stopped at.
         */
        public readonly int $offset,
    ) {}
}
