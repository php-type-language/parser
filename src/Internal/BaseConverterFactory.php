<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use TypeLang\Parser\Internal\BaseConverter\BaseConverterInterface;
use TypeLang\Parser\Internal\BaseConverter\GmpBaseConverter;
use TypeLang\Parser\Internal\BaseConverter\NativeBaseConverter;

/**
 * Selects the base converter for the current environment.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class BaseConverterFactory
{
    /**
     * @var list<BaseConverterInterface>
     */
    private readonly array $converters;

    /**
     * @param iterable<mixed, BaseConverterInterface> $converters
     */
    public function __construct(iterable $converters)
    {
        if ($converters instanceof \Traversable) {
            $converters = \iterator_to_array($converters, false);
        }

        $this->converters = \array_values($converters);
    }

    public static function default(): self
    {
        return new self([
            new GmpBaseConverter(),
        ]);
    }

    /**
     * Returns the first available converter or {@see NativeBaseConverter}.
     */
    public function select(): BaseConverterInterface
    {
        foreach ($this->converters as $converter) {
            if ($converter->isAvailable()) {
                return $converter;
            }
        }

        return new NativeBaseConverter();
    }
}
