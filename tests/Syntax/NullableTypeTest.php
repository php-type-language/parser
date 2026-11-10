<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The question mark before a type (grammar.pp3).
 */
final class NullableTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validNullableDataProvider(): iterable
    {
        yield 'name' => ['?int'];
        yield 'qualified name' => ['?\\Foo\\Bar'];
        yield 'virtual name' => ['?non-empty-string'];
        yield 'keyword' => ['?is'];
        yield 'null' => ['?null'];
        yield 'true' => ['?true'];
        yield 'int literal' => ['?42'];
        yield 'negative int literal' => ['?-42'];
        yield 'float literal' => ['?1.5'];
        yield 'string literal' => ["?'text'"];
        yield 'this' => ['?$this'];
        yield 'class constant' => ['?Foo::BAR'];
        yield 'class constant mask' => ['?Foo::BAR_*'];
        yield 'global mask' => ['?PHP_*'];
        yield 'mask of a namespace' => ['?Path\\To\\*'];

        yield 'list' => ['?int[]'];
        yield 'two lists' => ['?int[][]'];
        yield 'access' => ['?T[K]'];
        yield 'access and list' => ['?T[K][]'];
        yield 'nullable key' => ['T[?K]'];
        yield 'nullable key of a nullable' => ['?T[?K]'];
        yield 'nullable keys' => ['T[?K][?L]'];
        yield 'nullable key with a list' => ['T[?K[]]'];
        yield 'nullable key with a nullable key' => ['T[?K[?L]]'];

        yield 'group' => ['?(int)'];
        yield 'mark over a group of a nullable' => ['?(?int)'];
        yield 'mark over a group of a nullable list' => ['?(?int[])'];
        yield 'mark over a group of a nullable in the brackets' => ['T[?(?K)]'];
        yield 'group of a nullable' => ['(?int)'];
        yield 'group of a nullable and a list' => ['(?int)[]'];
        yield 'group of a nullable and an access' => ['(?T)[K]'];
        yield 'group of a list' => ['?(int[])'];
        yield 'group with a list' => ['?(int)[]'];
        yield 'nullable in a nullable' => ['?(?int)'];
        yield 'nullable in a nullable in a nullable' => ['?(?(?int))'];
        yield 'nullable key in a group' => ['T[(?K)]'];

        yield 'space behind the mark' => ['? int'];
        yield 'spaces behind the mark' => ['?    int'];
        yield 'tab behind the mark' => ["?\tint"];
        yield 'line break behind the mark' => ["?\nint"];
        yield 'carriage return behind the mark' => ["?\r\nint"];
        yield 'block comment behind the mark' => ['?/* c */int'];
        yield 'line comment behind the mark' => ["? // c\nint"];
        yield 'hash comment behind the mark' => ["? # c\nint"];
        yield 'space before the mark' => ['  ?int'];
        yield 'space behind a group mark' => ['? (int)'];
        yield 'spaces inside of the suffixes' => ['? int [ ]'];
        yield 'spaces inside of the key' => ['? T [ ? K ]'];
        yield 'comment behind the nullable' => ['?int // c'];
        yield 'comments everywhere' => ['/* a */ ? /* b */ T /* c */ [ /* d */ ? /* e */ K /* f */ ] /* g */'];
    }

    #[DataProvider('validNullableDataProvider')]
    public function testNullableIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidNullableDataProvider(): iterable
    {
        yield 'mark alone' => ['?'];
        yield 'mark and a space' => ['? '];
        yield 'mark and a comment' => ['? /* c */'];
        yield 'two marks' => ['??int'];
        yield 'two marks with a space' => ['? ?int'];
        yield 'two marks over a group' => ['??(int)'];
        yield 'two marks over a list' => ['??int[]'];
        yield 'two marks over a union' => ['??A|B'];
        yield 'two marks over an intersection' => ['??A&B'];
        yield 'three marks' => ['???int'];
        yield 'three marks with spaces' => ['? ? ?int'];
        yield 'mark behind the type' => ['int?'];
        yield 'mark behind the type with a space' => ['int ?'];
        yield 'mark between the type and a list' => ['int?[]'];
        yield 'mark in front of the brackets' => ['?[]'];
        yield 'mark in front of the brackets with a key' => ['?[K]'];
        yield 'marks around' => ['?int?'];
        yield 'mark behind a group' => ['(int)?'];
        yield 'mark in a group alone' => ['(?)'];
        yield 'mark in the empty group' => ['?()'];
        yield 'mark and a star' => ['?*'];
        yield 'mark and a colon' => ['?::'];
        yield 'mark and a closing bracket' => ['?]'];
        yield 'mark and a closing parenthesis' => ['?)'];
        yield 'mark in the brackets alone' => ['T[?]'];
        yield 'two marks in the brackets' => ['T[??K]'];
        yield 'two marks in the nested brackets' => ['T[K[??L]]'];
        yield 'three marks in the brackets' => ['T[???K]'];
        yield 'three marks in the nested brackets' => ['T[K[???L]]'];
        yield 'type behind' => ['?int string'];
        yield 'type behind with no space' => ['?int,string'];
        yield 'two nullables' => ['?int ?string'];
        yield 'two nullable groups' => ['(?int)(?string)'];
        yield 'mark in a comment only' => ['int /* ? */ string'];
        yield 'comment swallows the type' => ['? // int'];
        yield 'comment swallows the closing bracket' => ['T[?K // ]'];
        yield 'mark glued to a name of a namespace' => ['Foo\\?Bar'];
        yield 'mark in a name' => ['in?t'];
        yield 'mark in a class constant' => ['Foo::?BAR'];
        yield 'mark behind the colons' => ['Foo::BAR?'];
    }

    #[DataProvider('invalidNullableDataProvider')]
    public function testNullableIsRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{int<1, max>}>
     */
    public static function depthDataProvider(): iterable
    {
        yield 'one' => [1];
        yield 'ten' => [10];
        yield 'twenty' => [20];
        yield 'two hundred' => [200];
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testNestedNullablesAreAccepted(int $depth): void
    {
        self::assertAccepted(\str_repeat('?(', $depth) . 'int' . \str_repeat(')', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testNestedNullablesNotClosedAreRejected(int $depth): void
    {
        self::assertRejected(\str_repeat('?(', $depth) . 'int' . \str_repeat(')', $depth - 1));
        self::assertRejected(\str_repeat('?(', $depth) . 'int' . \str_repeat(')', $depth + 1));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testNullableKeysInsideOfKeysAreAccepted(int $depth): void
    {
        self::assertAccepted('T' . \str_repeat('[?K', $depth) . \str_repeat(']', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testNullableKeysInsideOfKeysNotClosedAreRejected(int $depth): void
    {
        self::assertRejected('T' . \str_repeat('[?K', $depth) . \str_repeat(']', $depth - 1));
    }

    /**
     * A type has one mark only, the second one is written with a group: `?(?int)`.
     *
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testManyMarksInARowAreRejected(int $depth): void
    {
        self::assertRejected(\str_repeat('?', $depth + 1) . 'int');
    }
}
