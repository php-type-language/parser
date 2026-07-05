<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser\PropertyAccessor;

interface PropertyAccessorInterface
{
    /**
     * @return iterable<array-key, mixed>
     */
    public function unwrap(object $object): iterable;
}
