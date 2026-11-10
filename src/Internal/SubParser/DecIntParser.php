<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

/**
 * Reads a decimal integer.
 *
 * ```
 *  42  -1_000  +7
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class DecIntParser extends IntParser {}
