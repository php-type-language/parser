<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type\Shape;

use TypeLang\Parser\Node\Type\TypeStatement;

interface FieldNodeInterface
{
    public function getValue(): TypeStatement;

    /**
     * @param class-string<FieldNodeInterface> $class
     */
    public function is(string $class): bool;
}
