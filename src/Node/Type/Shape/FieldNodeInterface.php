<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Type\TypeStatement;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
interface FieldNodeInterface
{
    public function getValue(): TypeStatement;

    /**
     * @param class-string<FieldNodeInterface> $class
     */
    public function is(string $class): bool;
}
