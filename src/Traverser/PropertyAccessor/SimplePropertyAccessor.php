<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser\PropertyAccessor;

final class SimplePropertyAccessor implements PropertyAccessorInterface
{
    /**
     * Skips static and hooked properties
     */
    private function shouldSkip(\ReflectionProperty $property): bool
    {
        if ($property->isStatic() || $property->isReadOnly()) {
            return true;
        }

        // Property hooks are available since PHP 8.4
        return \method_exists($property, 'hasHooks')
            && $property->hasHooks();
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
