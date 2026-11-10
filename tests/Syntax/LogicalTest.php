<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unions, intersections, nullable types, suffixes and groups
 * (grammar/logical.pp3 and grammar/wrapping.pp3).
 */
final class LogicalTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validTypeDataProvider(): iterable
    {
        yield 'union' => ['int|string'];
        yield 'union with spaces' => ['int | string'];
        yield 'union of three' => ['int|string|bool'];
        yield 'union with a repeated type' => ['int|string|int'];
        yield 'union of literals' => ["1|2|'a'|true|null"];
        yield 'union of constants' => ['Foo::A|Foo::B'];
        yield 'union of masks' => ['Foo::*|Bar::*'];
        yield 'union of shapes' => ['array{a: int}|array{b: string}'];
        yield 'union of callables' => ['callable(): int|callable(): string'];

        yield 'intersection' => ['A&B'];
        yield 'intersection with spaces' => ['A & B'];
        yield 'intersection of three' => ['A&B&C'];
        yield 'intersection of generics' => ['Foo<int>&Bar<string>'];

        yield 'intersection binds stronger' => ['A&B|C'];
        yield 'intersection on the right' => ['A|B&C'];
        yield 'two intersections' => ['A&B|C&D'];
        yield 'grouped intersection' => ['(A&B)|C'];
        yield 'grouped union' => ['A&(B|C)'];
        yield 'two grouped unions' => ['(A|B)&(C|D)'];
        yield 'nested groups' => ['((A&B)|C)&D'];

        yield 'nullable' => ['?int'];
        yield 'nullable with a space' => ['? int'];
        yield 'nullable with a tab' => ["?\tint"];
        yield 'nullable list' => ['?int[]'];
        yield 'nullable generic' => ['?Foo<int>'];
        yield 'nullable callable' => ['?callable(): int'];
        yield 'nullable group' => ['?(A|B)'];
        yield 'nullable this' => ['?$this'];
        yield 'nullable static' => ['?static'];
        yield 'nullable literal' => ["?'a'"];
        yield 'nullable constant' => ['?Foo::BAR'];
        yield 'nullable void' => ['?void'];
        yield 'grouped nullable' => ['(?int)'];
        yield 'grouped nullable in a union' => ['(?int)|string'];
        yield 'grouped nullable on the right' => ['int|(?string)'];
        yield 'list of nullables' => ['(?int)[]'];
        yield 'nullable in front of a union' => ['?int|string'];
        yield 'nullable on the right of a union' => ['int|?string'];
        yield 'nullable in front of an intersection' => ['?int&string'];
        yield 'nullable on the right of an intersection' => ['A&?B'];

        yield 'list' => ['int[]'];
        yield 'list of lists' => ['int[][]'];
        yield 'list with a space inside' => ['int[ ]'];
        yield 'list of a group' => ['(A|B)[]'];
        yield 'list of a generic' => ['Foo<int>[]'];
        yield 'list of this' => ['$this[]'];
        yield 'list of a literal' => ["'a'[]"];
        yield 'list of a constant' => ['Foo::BAR[]'];
        yield 'list in a union' => ['int[]|null'];
        yield 'list of a union item' => ['int|string[]'];

        yield 'offset' => ['T[K]'];
        yield 'offset with spaces' => ['T[ K ]'];
        yield 'offset by a literal' => ["T['a']"];
        yield 'offset by an integer' => ['T[-1]'];
        yield 'offset by a constant' => ['T[Foo::BAR]'];
        yield 'offset by a union' => ['T[K|L]'];
        yield 'offset by a generic' => ['T[Foo<int>]'];
        yield 'offset by a callable' => ['T[callable(): int]'];
        yield 'offset by an offset' => ['T[T[K]]'];
        yield 'two offsets' => ['T[K][L]'];
        yield 'offset and a list' => ['T[K][]'];
        yield 'list and an offset' => ['T[][K]'];
        yield 'offset of a shape' => ['array{a: int}["a"]'];
        yield 'offset of this' => ['$this[K]'];
        yield 'offset of a group' => ['(T)[K]'];
        yield 'offset in a union' => ['T[K]|U'];
        yield 'space before the bracket of an offset' => ['T [K]'];
        yield 'line break before the bracket of an offset' => ["T\n[K]"];
        yield 'comment before the bracket of an offset' => ['T/* c */[K]'];

        yield 'group' => ['(int)'];
        yield 'group of a union' => ['(int|string)'];
        yield 'group twice' => ['((int))'];
        yield 'group in a union' => ['(int)|(string)'];
    }

    #[DataProvider('validTypeDataProvider')]
    public function testTypeIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidTypeDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'lone bar' => ['|'];
        yield 'trailing bar' => ['int|'];
        yield 'leading bar' => ['|int'];
        yield 'double bar' => ['int||string'];
        yield 'trailing ampersand' => ['int&'];
        yield 'leading ampersand' => ['&int'];
        yield 'double ampersand' => ['int&&string'];
        yield 'lone ampersand' => ['&'];

        yield 'lone question mark' => ['?'];
        yield 'double question mark' => ['??int'];
        yield 'question mark behind' => ['int?'];
        yield 'nullable behind a nullable' => ['?int?'];
        yield 'question mark and a pipe' => ['?|int'];
        yield 'question mark and brackets' => ['?[]'];

        yield 'lone brackets' => ['[]'];
        yield 'brackets with a type' => ['[int]'];
        yield 'brackets before a type' => ['[int][]'];
        yield 'unclosed bracket' => ['int['];
        yield 'extra bracket' => ['int]'];
        yield 'unclosed offset' => ['T[K'];

        yield 'empty parentheses' => ['()'];
        yield 'empty parentheses with a space' => ['( )'];
        yield 'unclosed parenthesis' => ['(int'];
        yield 'extra parenthesis' => ['int)'];
        yield 'two groups in a row' => ['(int)(string)'];
        yield 'group with a trailing bar' => ['(int|)'];
        yield 'group with a leading bar' => ['(|int)'];
    }

    #[DataProvider('invalidTypeDataProvider')]
    public function testTypeIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
