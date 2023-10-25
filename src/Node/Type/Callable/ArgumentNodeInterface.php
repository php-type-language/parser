<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Callable;

use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
interface ArgumentNodeInterface
{
    public function getType(): TypeStatement;

    /**
     * @param class-string<ArgumentNodeInterface> $class
     */
    public function is(string $class): bool;
}
