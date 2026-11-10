<?php

declare(strict_types=1);

namespace TypeLang\Parser;

/**
 * Configures language features supported by the parser.
 *
 * Each flag enables or disables one language feature. When a feature is
 * disabled, the parser reports the corresponding syntax as not allowed.
 * All features are enabled by default.
 *
 * ```
 * $parser = new TypeParser(new TypeParserFeatures(
 *     conditions: false,
 * ));
 *
 * $parser->parse('T is 42 ? U : V');
 * // => Error: Conditional expressions not allowed in ...
 * ```
 */
final class TypeParserFeatures
{
    public function __construct(
        /**
         * Enables or disables support for union types such as `T|U`
         */
        public readonly bool $unions = true,
        /**
         * Enables or disables support for intersection types such as `T&U`
         */
        public readonly bool $intersections = true,
        /**
         * Enables or disables support for nullable types such as `?T`
         */
        public readonly bool $nullables = true,
        /**
         * Enables or disables support for short list types such as `T[]`
         */
        public readonly bool $lists = true,
        /**
         * Enables or disables support for offset access types such as `T[U]`
         */
        public readonly bool $offsets = true,
        /**
         * Enables or disables support for conditional types
         * such as `T is U ? A : B`
         */
        public readonly bool $conditions = true,
        /**
         * Enables or disables support for generic type arguments
         * such as `T<U, V>`
         */
        public readonly bool $generics = true,
        /**
         * Enables or disables support for wildcard generic arguments
         * such as `T<*>`
         */
        public readonly bool $wildcards = true,
        /**
         * Enables or disables support for call-site variance of generic
         * arguments such as `T<covariant U>`
         */
        public readonly bool $hints = true,
        /**
         * Enables or disables support for callable types
         * such as `callable(T): U`
         */
        public readonly bool $callables = true,
        /**
         * Enables or disables support for template parameters of callable
         * types such as `callable<T of U>(T): T`
         */
        public readonly bool $templates = true,
        /**
         * Enables or disables support for default values of callable
         * parameters such as `callable(T $a = U): V`
         */
        public readonly bool $defaults = true,
        /**
         * Enables or disables support for shape types such as `array{key: T}`
         */
        public readonly bool $shapes = true,
        /**
         * Enables or disables support for class constant types
         * such as `Foo::BAR`
         */
        public readonly bool $constants = true,
        /**
         * Enables or disables support for constant masks such as `Foo::BAR_*`
         */
        public readonly bool $masks = true,
        /**
         * Enables or disables support for literal types such as `42` or `'foo'`
         */
        public readonly bool $literals = true,
    ) {}

    /**
     * Creates a new feature set with one or more feature flags overridden.
     *
     * Any feature not explicitly specified retains its current value.
     *
     * ```
     * $features = $features->with(
     *     conditions: true,
     *     hints: false,
     * );
     * ```
     */
    public function with(bool ...$features): self
    {
        /** @var array<non-empty-string, bool> $arguments */
        $arguments = [...\get_object_vars($this), ...$features];

        return new self(...$arguments);
    }
}
