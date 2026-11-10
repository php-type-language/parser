<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Shapes of a named type (grammar/shapes.pp3).
 */
final class ShapesTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validShapeDataProvider(): iterable
    {
        yield 'empty' => ['array{}'];
        yield 'any name' => ['Foo{}'];
        yield 'list' => ['list{int, string}'];
        yield 'object' => ['object{a: int}'];
        yield 'fully qualified name' => ['\\Foo\\Bar{a: int}'];
        yield 'non empty array' => ['non-empty-array{a: int}'];
        yield 'space before the brace' => ['array {a: int}'];
        yield 'line break before the brace' => ["array\n{a: int}"];
        yield 'comment before the brace' => ['array/* c */{a: int}'];
        yield 'space before the brace of an empty shape' => ['array {}'];

        yield 'implicit field' => ['array{int}'];
        yield 'implicit fields' => ['array{int, string}'];
        yield 'implicit fields without a space' => ['array{int,string}'];
        yield 'implicit field with a generic' => ['array{Foo<int>, string}'];
        yield 'implicit field with a name of a field' => ['array{a}'];
        yield 'named field' => ['array{a: int}'];
        yield 'named field without a space' => ['array{a:int}'];
        yield 'named field with a space before the colon' => ['array{a : int}'];
        yield 'named fields' => ['array{a: int, b: string}'];
        yield 'trailing comma' => ['array{a: int, b: string,}'];
        yield 'hyphen in a key' => ['array{a-b: int}'];
        yield 'keyword as a key' => ['array{is: int}'];
        yield 'builtin as a key' => ['array{int: int}'];
        yield 'class as a key' => ['array{class: int}'];
        yield 'null as a key' => ['array{null: int}'];
        yield 'true as a key' => ['array{true: int}'];
        yield 'mixed implicit and named' => ['array{int, a: string}'];

        yield 'optional' => ['array{a?: int}'];
        yield 'optional with a space' => ['array{a ?: int}'];
        yield 'optional without spaces' => ['array{a?:int, b?: string}'];
        yield 'optional literal key' => ['array{true?: int}'];
        yield 'nullable value' => ['array{a: ?int}'];

        yield 'integer key' => ['array{0: int}'];
        yield 'negative integer key' => ['array{-1: int}'];
        yield 'hexadecimal key' => ['array{0x1: int}'];
        yield 'optional integer key' => ['array{0?: int}'];
        yield 'string key' => ["array{'a': int}"];
        yield 'double quoted key' => ['array{"a": int}'];
        yield 'string key with a space' => ["array{'a b': int}"];
        yield 'empty string key' => ["array{'': int}"];
        yield 'optional string key' => ["array{'a'?: int}"];
        yield 'constant key' => ['array{Foo::BAR: int}'];
        yield 'optional constant key' => ['array{Foo::BAR?: int}'];
        yield 'class name key' => ['array{Foo::class: int}'];

        yield 'nested' => ['array{a: array{b: array{c: int}}}'];
        yield 'value is a union' => ['array{a: int|string}'];
        yield 'value is a callable' => ['array{a: callable(): int, b: string}'];
        yield 'value is a generic' => ['array{a: Foo<int>}'];
        yield 'value is a list' => ['array{a: int[]}'];
        yield 'value is a condition' => ['array{a: T is int ? A : B}'];
        yield 'value is a grouped condition' => ['array{a: (T is int ? A : B)}'];

        yield 'unsealed' => ['array{...}'];
        yield 'unsealed behind fields' => ['array{a: int, ...}'];
        yield 'unsealed behind implicit fields' => ['array{int, ...}'];
        yield 'unsealed with a trailing comma' => ['array{a: int, ...,}'];
        yield 'unsealed with a space' => ['array{a: int, ... }'];
        yield 'unsealed with arguments' => ['array{...<K, V>}'];
        yield 'unsealed with arguments behind fields' => ['array{a: int, ...<K, V>}'];
        yield 'unsealed with one argument' => ['list{int, ...<string>}'];
        yield 'unsealed with arguments and a comma' => ['array{a: int, ...<K, V>,}'];
        yield 'unsealed with a space before the arguments' => ['array{... <K, V>}'];
        yield 'unsealed optional field' => ['array{a?: int, ...}'];

        yield 'suffix' => ['array{a: int}[]'];
        yield 'offset' => ['array{a: int}["a"]'];
        yield 'nullable' => ['?array{a: int}'];
        yield 'union' => ['array{a: int}|array{b: string}'];
        yield 'argument of a generic' => ['Foo<array{a: int}, list{int}>'];
    }

    #[DataProvider('validShapeDataProvider')]
    public function testShapeIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidShapeDataProvider(): iterable
    {
        yield 'unclosed' => ['array{'];
        yield 'unclosed behind a field' => ['array{a: int'];
        yield 'only a comma' => ['array{,}'];
        yield 'empty field' => ['array{a: int,,}'];
        yield 'no type' => ['array{a:}'];
        yield 'no key' => ['array{:int}'];
        yield 'two fields without a comma' => ['array{a: int b: string}'];
        yield 'question mark before a key' => ['array{?a: int}'];
        yield 'float key' => ['array{1.5: int}'];
        yield 'variable key' => ['array{$a: int}'];
        yield 'generic key' => ['array{Foo<int>: int}'];
        yield 'union key' => ['array{int|string: int}'];

        yield 'unsealed behind a field without a comma' => ['array{a: int ...}'];
        yield 'unsealed with a comma in front of it and no fields' => ['array{, ...}'];
        yield 'unsealed before a field' => ['array{..., a: int}'];
        yield 'unsealed between fields' => ['array{a: int, ..., b: string}'];
        yield 'unsealed with a type' => ['array{a: int, ...int}'];
        yield 'unsealed with a name' => ['array{a: int, ...: int}'];
        yield 'unsealed with empty arguments' => ['array{...<>}'];
        yield 'unsealed twice' => ['array{a: int, ..., ...}'];

        yield 'arguments and a shape' => ['Foo<int>{a: int}'];
        yield 'arguments and an empty shape' => ['Foo<int>{}'];
        yield 'arguments and an unsealed shape' => ['array<K, V>{a: int, ...}'];
        yield 'arguments on both sides of a shape' => ['array<K>{a: int, ...<K, V>}'];
    }

    #[DataProvider('invalidShapeDataProvider')]
    public function testShapeIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
