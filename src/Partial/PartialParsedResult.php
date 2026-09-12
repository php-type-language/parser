<?php

declare(strict_types=1);

namespace TypeLang\Parser\Partial;

/**
 * A source the grammar has read only the beginning of.
 *
 * The type is the one the read part describes, and the rest of the source
 * begins at the {@see $offset}.
 *
 * ```php
 *  $source = 'array{a: int} and more';
 *  $result = $parser->partial($source);
 *
 *  echo \substr($source, $result->offset);
 *  // => " and more"
 * ```
 */
final class PartialParsedResult extends SuccessfulParsedResult {}
