<?php

declare(strict_types=1);

namespace TypeLang\Parser;

/**
 * Configures language features supported by the parser.
 *
 * Feature flags allow enabling or disabling individual language constructs.
 * When a feature is disabled, the parser will treat the corresponding syntax
 * as unsupported and report an error.
 *
 * ```
 * $parser = new Parser(new ParserFeatures(
 *     conditions: false,
 * ));
 *
 * $parser->parse('T is 42 ? U : V');
 * // => Error: Conditional expressions not allowed in ...
 * ```
 */
final class TypeParserFeatures
{
    public const CONDITIONAL_FEATURES_DEFAULT_VALUE = true;
    public const SHAPES_FEATURES_DEFAULT_VALUE = true;
    public const CALLABLES_FEATURES_DEFAULT_VALUE = true;
    public const LITERALS_FEATURES_DEFAULT_VALUE = true;
    public const GENERICS_FEATURES_DEFAULT_VALUE = true;
    public const UNION_FEATURES_DEFAULT_VALUE = true;
    public const INTERSECTION_FEATURES_DEFAULT_VALUE = true;
    public const LIST_FEATURES_DEFAULT_VALUE = true;
    public const OFFSETS_FEATURES_DEFAULT_VALUE = true;
    public const HINTS_FEATURES_DEFAULT_VALUE = true;

    public function __construct(
        /**
         * Enables or disables support for conditional types such as `T ? U : V`
         */
        public readonly bool $conditions = self::CONDITIONAL_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for shape types such as `T{key: U}`
         */
        public readonly bool $shapes = self::SHAPES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for callable types such as `fn(T, U): V`
         */
        public readonly bool $callables = self::CALLABLES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for literal types such as `42` or `'foo'`
         */
        public readonly bool $literals = self::LITERALS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for generic type arguments such as `T<U, V>`
         */
        public readonly bool $generics = self::GENERICS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for union types such as `T | U`
         */
        public readonly bool $unions = self::UNION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for intersection types such as `T & U`
         */
        public readonly bool $intersections = self::INTERSECTION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for short list types such as `T[]`
         */
        public readonly bool $lists = self::LIST_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for offset access types such as `T[U]`
         */
        public readonly bool $offsets = self::OFFSETS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for generic variance hints such as `T<out U, in V>`
         */
        public readonly bool $hints = self::HINTS_FEATURES_DEFAULT_VALUE,
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
