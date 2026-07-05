<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\NamedTypeNode;

/**
 * Tests that each grammar feature can be disabled through the parser options
 * and that disabled constructs are rejected.
 *
 * @phpstan-import-type ParserOptionsType from \TypeLang\Parser\Tests\TestCase
 */
#[Group('unit'), Group('type-lang/parser')]
final class FeatureToggleTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{array<non-empty-string, bool>, non-empty-string, non-empty-string}>
     */
    public static function disabledFeatureDataProvider(): iterable
    {
        yield 'literals' => [['literals' => false], '42', 'Literal values not allowed'];
        yield 'generics' => [['generics' => false], 'T<U>', 'Template arguments not allowed'];
        yield 'shapes' => [['shapes' => false], 'array{a: int}', 'Shape fields not allowed'];
        yield 'callables' => [['callables' => false], 'foo(): void', 'Callable types not allowed'];
        yield 'unions' => [['unions' => false], 'A|B', 'Union types not allowed'];
        yield 'intersections' => [['intersections' => false], 'A&B', 'Intersection types not allowed'];
        yield 'lists' => [['lists' => false], 'int[]', 'Square bracket list types not allowed'];
        yield 'offsets' => [['offsets' => false], 'T[U]', 'Type offsets not allowed'];
        yield 'conditions' => [['conditions' => false], 'A is B ? C : D', 'Conditional expressions not allowed'];
        yield 'attributes' => [['attributes' => false], 'T<#[a] U>', 'Template argument attributes not allowed'];
        yield 'hints' => [['hints' => false], 'T<in U>', 'Template argument hints not allowed'];
    }

    /**
     * @param array<non-empty-string, bool> $options
     */
    #[DataProvider('disabledFeatureDataProvider')]
    public function testDisabledFeatureIsRejected(array $options, string $type, string $message): void
    {
        $this->expectParsingException($message);

        $this->parse($type, $options);
    }

    public function testTrailingTextIsRejectedInStrictMode(): void
    {
        $this->expectParsingException();

        $this->parse('int and more text');
    }

    public function testTrailingTextIsAllowedInTolerantMode(): void
    {
        $result = $this->parseTolerant('int and more text');
        $type = $result->type;

        self::assertInstanceOf(NamedTypeNode::class, $type);
        self::assertSame('int', $type->name->toString());
    }
}
