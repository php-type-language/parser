<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\TypeParserFeatures;

final class TypeParserFeaturesTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{
     *     non-empty-string,
     *     \Closure(TypeParserFeatures):bool,
     *     bool
     * }>
     */
    public static function featureDataProvider(): iterable
    {
        yield 'conditions' => [
            'conditions',
            static fn(TypeParserFeatures $features): bool => $features->conditions,
            TypeParserFeatures::CONDITIONAL_FEATURES_DEFAULT_VALUE,
        ];

        yield 'shapes' => [
            'shapes',
            static fn(TypeParserFeatures $features): bool => $features->shapes,
            TypeParserFeatures::SHAPES_FEATURES_DEFAULT_VALUE,
        ];

        yield 'callables' => [
            'callables',
            static fn(TypeParserFeatures $features): bool => $features->callables,
            TypeParserFeatures::CALLABLES_FEATURES_DEFAULT_VALUE,
        ];

        yield 'literals' => [
            'literals',
            static fn(TypeParserFeatures $features): bool => $features->literals,
            TypeParserFeatures::LITERALS_FEATURES_DEFAULT_VALUE,
        ];

        yield 'generics' => [
            'generics',
            static fn(TypeParserFeatures $features): bool => $features->generics,
            TypeParserFeatures::GENERICS_FEATURES_DEFAULT_VALUE,
        ];

        yield 'unions' => [
            'unions',
            static fn(TypeParserFeatures $features): bool => $features->unions,
            TypeParserFeatures::UNION_FEATURES_DEFAULT_VALUE,
        ];

        yield 'intersections' => [
            'intersections',
            static fn(TypeParserFeatures $features): bool => $features->intersections,
            TypeParserFeatures::INTERSECTION_FEATURES_DEFAULT_VALUE,
        ];

        yield 'lists' => [
            'lists',
            static fn(TypeParserFeatures $features): bool => $features->lists,
            TypeParserFeatures::LIST_FEATURES_DEFAULT_VALUE,
        ];

        yield 'offsets' => [
            'offsets',
            static fn(TypeParserFeatures $features): bool => $features->offsets,
            TypeParserFeatures::OFFSETS_FEATURES_DEFAULT_VALUE,
        ];

        yield 'hints' => [
            'hints',
            static fn(TypeParserFeatures $features): bool => $features->hints,
            TypeParserFeatures::HINTS_FEATURES_DEFAULT_VALUE,
        ];
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureIsEnabledByDefault(string $feature, \Closure $read, bool $default): void
    {
        self::assertTrue($default);
        self::assertSame($default, $read(new TypeParserFeatures()));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeDisabledUsingTheConstructor(string $feature, \Closure $read, bool $default): void
    {
        self::assertFalse($read(new TypeParserFeatures(...[$feature => false])));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeDisabledUsingTheWithMethod(string $feature, \Closure $read, bool $default): void
    {
        self::assertFalse($read((new TypeParserFeatures())->with(...[$feature => false])));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeEnabledUsingTheWithMethod(string $feature, \Closure $read, bool $default): void
    {
        $features = new TypeParserFeatures(...[$feature => false]);

        self::assertTrue($read($features->with(...[$feature => true])));
    }

    #[Test]
    public function withReturnsANewInstance(): void
    {
        $features = new TypeParserFeatures();

        self::assertNotSame($features, $features->with(shapes: false));
    }

    #[Test]
    public function withDoesNotModifyTheOriginalInstance(): void
    {
        $features = new TypeParserFeatures();
        $features->with(shapes: false);

        self::assertTrue($features->shapes);
    }

    #[Test]
    public function withKeepsTheNonSpecifiedFeatures(): void
    {
        $features = (new TypeParserFeatures(literals: false))
            ->with(shapes: false);

        self::assertFalse($features->literals);
        self::assertFalse($features->shapes);
        self::assertTrue($features->generics);
    }

    #[Test]
    public function withCanOverrideSeveralFeaturesAtOnce(): void
    {
        $features = (new TypeParserFeatures())->with(
            unions: false,
            intersections: false,
        );

        self::assertFalse($features->unions);
        self::assertFalse($features->intersections);
    }

    #[Test]
    public function withoutArgumentsKeepsEveryFeature(): void
    {
        $features = new TypeParserFeatures(callables: false, offsets: false);

        self::assertEquals($features, $features->with());
    }

    #[Test]
    public function withRejectsAnUnknownFeature(): void
    {
        $this->expectException(\Error::class);

        /** @phpstan-ignore-next-line : An unknown feature name is expected here */
        (new TypeParserFeatures())->with(unknownFeature: false);
    }
}
