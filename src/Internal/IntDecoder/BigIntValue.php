<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\IntDecoder;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @readonly
 */
final class BigIntValue
{
    public function __construct(
        public int $value,
        /**
         * @var numeric-string
         */
        public string $decimal,
    ) {}
}
