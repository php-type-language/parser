<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A name with a constant, with template arguments or with shape fields behind
 * it, and what may stand around them (grammar.pp3).
 */
final class NamedTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validConstantDataProvider(): iterable
    {
        // What stands before the double colon
        yield 'qualified class' => ['Foo\\Bar::BAR'];
        yield 'fully qualified class' => ['\\Foo\\Bar::BAR'];
        yield 'namespace relative class' => ['namespace\\Foo::BAR'];
        yield 'self' => ['self::BAR'];
        yield 'static' => ['static::BAR'];
        yield 'parent' => ['parent::BAR'];
        yield 'int' => ['int::BAR'];
        yield 'string' => ['string::BAR'];
        yield 'array' => ['array::BAR'];
        yield 'list' => ['list::BAR'];
        yield 'mixed' => ['mixed::BAR'];
        yield 'object' => ['object::BAR'];
        yield 'iterable' => ['iterable::BAR'];
        yield 'callable' => ['callable::BAR'];
        yield 'virtual name' => ['class-string::BAR'];
        yield 'virtual name with hyphens' => ['non-empty-string::BAR'];

        // What stands behind the double colon
        yield 'constant in lower case' => ['Foo::bar'];
        yield 'constant in camel case' => ['Foo::Bar'];
        yield 'constant with a digit' => ['Foo::BAR_1'];
        yield 'constant with an underscore in front' => ['Foo::_BAR'];
        yield 'class' => ['Foo::class'];
        yield 'class in upper case' => ['Foo::CLASS'];
        yield 'true' => ['Foo::true'];
        yield 'null' => ['Foo::null'];
        yield 'type name' => ['Foo::int'];
        yield 'array name' => ['Foo::array'];

        // Masks of constants
        yield 'mask' => ['Foo::BAR_*'];
        yield 'lone star' => ['Foo::*'];
        yield 'star in front' => ['Foo::*_BAR'];
        yield 'star in the middle' => ['Foo::A*B'];
        yield 'two parts around a star' => ['Foo::A_*_B'];
        yield 'mask of a qualified class' => ['Foo\\Bar::BAR_*'];
        yield 'mask of a fully qualified class' => ['\\Foo::BAR_*'];
        yield 'mask of self' => ['self::BAR_*'];
        yield 'mask of static' => ['static::BAR_*'];
        yield 'mask of parent' => ['parent::BAR_*'];
        yield 'mask of int' => ['int::BAR_*'];
        yield 'mask of array' => ['array::BAR_*'];

        // Around a constant
        yield 'constant in a union' => ['Foo|Bar::BAR'];
        yield 'constant in front of a union' => ['Foo::BAR|Bar'];
        yield 'nullable constant' => ['?Foo::BAR'];
        yield 'list of a mask' => ['Foo::BAR_*[]'];
        yield 'list of constants' => ['Foo::BAR[]'];
    }

    #[DataProvider('validConstantDataProvider')]
    public function testConstantIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * Only a name may stand before the double colon, and nothing but a word or a
     * mask behind it.
     *
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidConstantDataProvider(): iterable
    {
        yield 'variable' => ['$a::BAR'];
        yield 'this' => ['$this::BAR'];
        yield 'mask of this' => ['$this::BAR_*'];
        yield 'class of this' => ['$this::class'];
        yield 'string' => ["'Foo'::BAR"];
        yield 'list' => ['Foo[]::BAR'];
        yield 'group' => ['(Foo)::BAR'];
        yield 'group class' => ['(Foo)::class'];
        yield 'group of a union' => ['(Foo|Bar)::class'];
        yield 'group of a generic' => ['(Foo<int>)::BAR'];
        yield 'group of a generic class' => ['(Foo<int>)::class'];
        yield 'group of a shape' => ['(Foo{a: int})::BAR'];
        yield 'group of a mask' => ['(Foo)::BAR_*'];
        yield 'two constants' => ['Foo::BAR::BAR'];
        yield 'constant and class' => ['Foo::BAR::class'];
        yield 'two classes' => ['Foo::class::class'];
        yield 'mask and constant' => ['Foo::*::BAR'];
        yield 'number' => ['Foo::1'];
        yield 'variable behind' => ['Foo::$bar'];
        yield 'call' => ['Foo::bar()'];
        yield 'hyphen in a constant' => ['Foo::BAR-BAR'];
        yield 'backslash in a constant' => ['Foo::BAR\\Baz'];
    }

    #[DataProvider('invalidConstantDataProvider')]
    public function testConstantIsRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * Template arguments, shape fields and a constant do not go together on
     * one name, none of the tools allows it.
     *
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidCombinationDataProvider(): iterable
    {
        yield 'constant behind the arguments' => ['Foo<int>::BAR'];
        yield 'mask behind the arguments' => ['Foo<int>::BAR_*'];
        yield 'lone star behind the arguments' => ['Foo<int>::*'];
        yield 'class behind the arguments' => ['Foo<int>::class'];
        yield 'constant behind the arguments of array' => ['array<int, string>::BAR'];
        yield 'constant behind the fields' => ['Foo{a: int}::BAR'];
        yield 'mask behind the fields' => ['Foo{a: int}::BAR_*'];
        yield 'constant behind the unsealed fields' => ['Foo{a: int, ...<T>}::BAR'];
        yield 'constant behind the fields of array' => ['array{a: int}::BAR'];
        yield 'arguments behind a constant' => ['Foo::BAR<int>'];
        yield 'arguments behind a mask' => ['Foo::BAR_*<int>'];
        yield 'fields behind a constant' => ['Foo::BAR{a: int}'];
        yield 'fields behind the arguments' => ['Foo<int>{a: int}'];
        yield 'arguments behind the fields' => ['Foo{a: int}<int>'];
        yield 'list of a constant behind the arguments' => ['Foo<int>::BAR[]'];
        yield 'constant with the arguments as a key' => ['array{Foo<int>::BAR: int}'];
        yield 'constant with the arguments behind as a key' => ['array{Foo::BAR<int>: int}'];
    }

    #[DataProvider('invalidCombinationDataProvider')]
    public function testCombinationIsRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validAroundDataProvider(): iterable
    {
        yield 'list of arguments' => ['Foo<int>[]'];
        yield 'access of arguments' => ['Foo<int>[K]'];
        yield 'list of fields' => ['Foo{a: int}[]'];
        yield 'access of fields' => ['Foo{a: int}[K]'];
        yield 'list of list of fields' => ['array{a: int}[][K]'];
        yield 'list of unsealed fields' => ['array{a: int, ...<K, V>}[]'];
        yield 'union of arguments' => ['Foo<int>|Bar<string>'];
        yield 'union of arguments and fields' => ['Foo<int>|Foo{a: int}'];
        yield 'intersection of arguments and fields' => ['Foo<int>&Foo{a: int}'];
        yield 'union of fields' => ['array{a: int}|B'];
        yield 'nullable fields' => ['?Foo{a: int}'];
        yield 'custom name with fields' => ['Foo{a: int}'];
        yield 'custom name with no fields' => ['Foo{}'];
        yield 'qualified custom name with fields' => ['\\Foo\\Bar{a: int}'];
    }

    #[DataProvider('validAroundDataProvider')]
    public function testSuffixesAndOperatorsBehindArgumentsAndFieldsAreAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validArgumentDataProvider(): iterable
    {
        yield 'trailing comma' => ['Foo<int,>'];
        yield 'four arguments' => ['Foo<A,B,C,D>'];
        yield 'wildcard first' => ['Foo<*,int>'];
        yield 'wildcard last' => ['Foo<A,*>'];
        yield 'nested' => ['Foo<int<A>>'];
        yield 'union' => ['Foo<A|B>'];
        yield 'list' => ['Foo<A[]>'];
        yield 'list of a shape' => ['Foo<array{a: int}[]>'];
        yield 'nullable' => ['Foo<?int>'];
        yield 'group' => ['Foo<(A)>'];
        yield 'class constant' => ['Foo<A::B>'];
        yield 'qualified name' => ['Foo<A\\B>'];
        yield 'string' => ["Foo<'a'>"];
        yield 'number' => ['Foo<1>'];
        yield 'true' => ['Foo<true>'];
        yield 'null' => ['Foo<null>'];
        yield 'comment behind' => ['Foo<A/* c */>'];
        yield 'comment in front' => ['Foo</* c */A>'];

        // covariant, contravariant and invariant in front of a type are a variance
        yield 'variance and a name' => ['Foo<covariant T>'];
        yield 'contravariance and a name' => ['Foo<contravariant T>'];
        yield 'invariance and a name' => ['Foo<invariant T>'];
        yield 'variance and a list' => ['Foo<covariant B[]>'];
        yield 'variance and a group of a union' => ['Foo<covariant (A|B)>'];
        yield 'variance and a class constant' => ['Foo<covariant A::B>'];
        yield 'variance and a qualified name' => ['Foo<covariant A\\B>'];
        yield 'variance and a nullable' => ['Foo<covariant ?int>'];
        yield 'variance and this' => ['Foo<covariant $this>'];
        yield 'variance and a string' => ["Foo<covariant 'a'>"];
        yield 'variance and a number' => ['Foo<covariant 1>'];
        yield 'variance and true' => ['Foo<covariant true>'];

        // A lone word is a name, not a variance
        yield 'lone covariant' => ['Foo<covariant>'];
        yield 'lone contravariant' => ['Foo<contravariant>'];
        yield 'lone invariant' => ['Foo<invariant>'];
        yield 'lone in' => ['Foo<in>'];
    }

    #[DataProvider('validArgumentDataProvider')]
    public function testArgumentsAreAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * A gap, a line break and a comment are allowed before the opening symbol
     * and behind a variance (question 7 of grammar-questions.md).
     *
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validGapDataProvider(): iterable
    {
        yield 'space before the arguments' => ['Foo <int>'];
        yield 'line break before the arguments' => ["Foo\n<int>"];
        yield 'comment before the arguments' => ['Foo/* c */<int>'];
        yield 'space before the fields' => ['array {a: int}'];
        yield 'line break before the fields' => ["array\n{a: int}"];
        yield 'comment before the fields' => ['array/* c */{a: int}'];
        yield 'space before the parameters' => ['callable (int)'];
        yield 'group behind a variance' => ['Foo<covariant (A)>'];
        yield 'group behind a variance in a union' => ['Foo<covariant (A|B)>'];
        yield 'group behind a variance with no space' => ['Foo<covariant(A|B)>'];
        yield 'callable of a word that is no variance' => ['Foo<A (B)>'];
        yield 'callable of in' => ['Foo<in (A|B)>'];
        yield 'call-like type' => ['Foo<callable(int)>'];
        yield 'call-like type with a space' => ['Foo<callable (int)>'];
        yield 'line break behind a variance' => ["Foo<covariant\nB>"];
        yield 'comment behind a variance' => ['Foo<covariant/* c */B>'];
    }

    #[DataProvider('validGapDataProvider')]
    public function testGapIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidArgumentDataProvider(): iterable
    {
        yield 'wildcard in a union' => ['Foo<*|int>'];
        yield 'two wildcards glued' => ['Foo<**>'];
        yield 'empty' => ['Foo<>'];
        yield 'comma alone' => ['Foo<,>'];
        yield 'two commas' => ['Foo<int,,string>'];
        yield 'not closed' => ['Foo<int'];
        yield 'not opened' => ['Foo int>'];
        yield 'closed twice' => ['Foo<int>>'];
        yield 'three words' => ['Foo<A B C>'];
    }

    #[DataProvider('invalidArgumentDataProvider')]
    public function testArgumentsAreRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validShapeDataProvider(): iterable
    {
        yield 'spaces only' => ['array{ }'];
        yield 'spaces around' => ['array{ a : int }'];
        yield 'spaces around an optional mark' => ['array{ a? : int }'];
        yield 'implicit fields' => ['array{a, b}'];
        yield 'explicit and implicit fields' => ['array{a: int, b}'];
        yield 'union and nullable fields' => ['array{A|B, ?int}'];
        yield 'virtual name as a key' => ['array{non-empty-string: int}'];
        yield 'class constant as a key' => ['array{Foo\\Bar::BAZ: int}'];
        yield 'optional class constant as a key' => ['array{Foo::BAR?: int}'];
        yield 'class named class as a key' => ['array{Foo::class: int}'];
        yield 'global constant as a key' => ['array{FOO: int}'];
        yield 'fully qualified name as a key' => ['array{\\Foo: int}'];
        yield 'qualified name as a key' => ['array{Foo\\Bar: int}'];
        yield 'qualified constant as a key' => ['array{Foo\\BAR: int}'];
        yield 'fully qualified constant as a key' => ['array{\\Foo\\BAR: int}'];
        yield 'optional qualified name as a key' => ['array{Foo\\Bar?: int}'];
        yield 'negative integer as a key' => ['array{-1: int}'];
        yield 'negative integer with a space as a key' => ['array{- 1: int}'];
        yield 'positive integer as a key' => ['array{+1: int}'];
        yield 'positive integer with a space as a key' => ['array{+ 1: int}'];
        yield 'class constant as an implicit field' => ['array{Foo::BAR, int}'];
        yield 'class constant mask as an implicit field' => ['array{Foo::BAR_*, int}'];
        yield 'nested shapes and arguments' => ['array{a: Foo<int>, b: Bar{c: int}}'];
        yield 'deeply nested' => ['array{a: array{b: Foo<array{c: int}>}}'];
        yield 'unsealed with an argument' => ['array{a: int, ...<K>}'];
        yield 'unsealed with a wildcard' => ['array{a: int, ...<*>}'];
        yield 'unsealed with a trailing comma in the arguments' => ['array{a: int, ...<K,>}'];
        yield 'unsealed with a comma behind' => ['array{a: int, ...<K>,}'];
        yield 'unsealed with a space before the arguments' => ['array{a: int, ... <K>}'];
        yield 'unsealed with the comma and the space behind' => ['array{a: int, ...<K, V>, }'];
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
        yield 'field behind the unsealed mark' => ['array{a: int, ..., b: int}'];
        yield 'field behind the unsealed arguments' => ['array{...<K>, a: int}'];
        yield 'two commas' => ['array{a: int,,b: int}'];
        yield 'two commas in front of the unsealed mark' => ['array{a: int,, ...}'];
        yield 'two words with no colon' => ['array{a int}'];
        yield 'colon with no key' => ['array{?: int}'];
        yield 'optional mark with no type' => ['array{a?}'];
        yield 'colon with no type' => ['array{a: }'];
        yield 'comma alone' => ['array{,}'];
        yield 'empty arguments of the unsealed mark' => ['array{a: int, ...<>}'];
        yield 'mask in a key' => ['array{PHP_*: int}'];
        yield 'optional mask in a key' => ['array{PHP_*?: int}'];
        yield 'qualified mask in a key' => ['array{Foo\\BAR_*: int}'];
        yield 'fully qualified mask in a key' => ['array{\\PHP_*: int}'];
        yield 'class constant mask in a key' => ['array{Foo::BAR_*: int}'];
        yield 'optional class constant mask in a key' => ['array{Foo::BAR_*?: int}'];
        yield 'class lone star mask in a key' => ['array{Foo::*: int}'];
        yield 'star in a key' => ['array{*: int}'];
        yield 'float in a key' => ['array{0.5: int}'];
        yield 'float with a sign in a key' => ['array{-1.5: int}'];
        yield 'float with a sign and a space in a key' => ['array{- 1.5: int}'];
        yield 'float with a plus and a space in a key' => ['array{+ 1.5: int}'];
        yield 'float with an exponent in a key' => ['array{1e3: int}'];
        yield 'variable in a key' => ['array{$a: int}'];
        yield 'this in a key' => ['array{$this: int}'];
    }

    #[DataProvider('invalidShapeDataProvider')]
    public function testShapeIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
