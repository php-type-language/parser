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
final readonly class TypeParserFeatures
{
    public const bool CONDITIONAL_FEATURES_DEFAULT_VALUE = true;
    public const bool SHAPES_FEATURES_DEFAULT_VALUE = true;
    public const bool CALLABLES_FEATURES_DEFAULT_VALUE = true;
    public const bool LITERALS_FEATURES_DEFAULT_VALUE = true;
    public const bool GENERICS_FEATURES_DEFAULT_VALUE = true;
    public const bool UNION_FEATURES_DEFAULT_VALUE = true;
    public const bool INTERSECTION_FEATURES_DEFAULT_VALUE = true;
    public const bool LIST_FEATURES_DEFAULT_VALUE = true;
    public const bool OFFSETS_FEATURES_DEFAULT_VALUE = true;
    public const bool HINTS_FEATURES_DEFAULT_VALUE = true;
    public const bool ATTRIBUTES_FEATURES_DEFAULT_VALUE = true;

    public function __construct(
        /**
         * Enables or disables support for conditional types such as `T ? U : V`
         */
        public bool $conditions = self::CONDITIONAL_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for shape types such as `T{key: U}`
         */
        public bool $shapes = self::SHAPES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for callable types such as `fn(T, U): V`
         */
        public bool $callables = self::CALLABLES_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for literal types such as `42` or `'foo'`
         */
        public bool $literals = self::LITERALS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for generic type arguments such as `T<U, V>`
         */
        public bool $generics = self::GENERICS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for union types such as `T | U`
         */
        public bool $unions = self::UNION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for intersection types such as `T & U`
         */
        public bool $intersections = self::INTERSECTION_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for short list types such as `T[]`
         */
        public bool $lists = self::LIST_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for offset access types such as `T[U]`
         */
        public bool $offsets = self::OFFSETS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for generic variance hints such as `T<out U, in V>`
         */
        public bool $hints = self::HINTS_FEATURES_DEFAULT_VALUE,
        /**
         * Enables or disables support for attributes such as `#[attr]`
         */
        public bool $attributes = self::ATTRIBUTES_FEATURES_DEFAULT_VALUE,
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
