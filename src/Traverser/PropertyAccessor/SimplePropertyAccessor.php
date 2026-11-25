<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser\PropertyAccessor;

final class SimplePropertyAccessor implements PropertyAccessorInterface
{
    /**
     * Skips static, readonly and hooked properties
     */
    private function shouldSkip(\ReflectionProperty $property): bool
    {
        if ($property->isStatic() || $property->isReadOnly()) {
            return true;
        }

        return \method_exists($property, 'hasHooks')
            && $property->hasHooks() === true;
    }

    public function unwrap(object $object): iterable
    {
        $reflection = new \ReflectionObject($object);

        foreach ($reflection->getProperties() as $property) {
            if ($this->shouldSkip($property)) {
                continue;
            }

            yield $property->getName() => $property->getValue($object);
        }
    }
}
