<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Node;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-covariant TResult of Node
 */
interface SubParserInterface
{
    /**
     * @param non-empty-string $raw
     * @param int<0, max> $offset
     * @return TResult
     */
    public function parse(string $raw, int $offset = 0): Node;
}
