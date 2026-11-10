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
     *     \Closure(TypeParserFeatures):bool
     * }>
     */
    public static function featureDataProvider(): iterable
    {
        yield 'unions' => [
            'unions',
            static fn(TypeParserFeatures $features): bool => $features->unions,
        ];

        yield 'intersections' => [
            'intersections',
            static fn(TypeParserFeatures $features): bool => $features->intersections,
        ];

        yield 'nullables' => [
            'nullables',
            static fn(TypeParserFeatures $features): bool => $features->nullables,
        ];

        yield 'lists' => [
            'lists',
            static fn(TypeParserFeatures $features): bool => $features->lists,
        ];

        yield 'offsets' => [
            'offsets',
            static fn(TypeParserFeatures $features): bool => $features->offsets,
        ];

        yield 'conditions' => [
            'conditions',
            static fn(TypeParserFeatures $features): bool => $features->conditions,
        ];

        yield 'generics' => [
            'generics',
            static fn(TypeParserFeatures $features): bool => $features->generics,
        ];

        yield 'wildcards' => [
            'wildcards',
            static fn(TypeParserFeatures $features): bool => $features->wildcards,
        ];

        yield 'hints' => [
            'hints',
            static fn(TypeParserFeatures $features): bool => $features->hints,
        ];

        yield 'callables' => [
            'callables',
            static fn(TypeParserFeatures $features): bool => $features->callables,
        ];

        yield 'templates' => [
            'templates',
            static fn(TypeParserFeatures $features): bool => $features->templates,
        ];

        yield 'defaults' => [
            'defaults',
            static fn(TypeParserFeatures $features): bool => $features->defaults,
        ];

        yield 'shapes' => [
            'shapes',
            static fn(TypeParserFeatures $features): bool => $features->shapes,
        ];

        yield 'constants' => [
            'constants',
            static fn(TypeParserFeatures $features): bool => $features->constants,
        ];

        yield 'masks' => [
            'masks',
            static fn(TypeParserFeatures $features): bool => $features->masks,
        ];

        yield 'literals' => [
            'literals',
            static fn(TypeParserFeatures $features): bool => $features->literals,
        ];
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureIsEnabledByDefault(string $feature, \Closure $read): void
    {
        self::assertTrue($read(new TypeParserFeatures()));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeDisabledUsingTheConstructor(string $feature, \Closure $read): void
    {
        self::assertFalse($read(new TypeParserFeatures(...[$feature => false])));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeDisabledUsingTheWithMethod(string $feature, \Closure $read): void
    {
        self::assertFalse($read((new TypeParserFeatures())->with(...[$feature => false])));
    }

    /**
     * @param non-empty-string $feature
     * @param \Closure(TypeParserFeatures):bool $read
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function eachFeatureCanBeEnabledUsingTheWithMethod(string $feature, \Closure $read): void
    {
        $features = new TypeParserFeatures(...[$feature => false]);

        self::assertTrue($read($features->with(...[$feature => true])));
    }

    /**
     * @param non-empty-string $feature
     */
    #[DataProvider('featureDataProvider')]
    #[Test]
    public function disablingOneFeatureKeepsTheOthersEnabled(string $feature): void
    {
        $features = new TypeParserFeatures(...[$feature => false]);

        foreach (self::featureDataProvider() as $name => [, $other]) {
            self::assertSame($name !== $feature, $other($features), $name);
        }
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
