<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The types joined with the bar (grammar.pp3).
 */
final class UnionTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validUnionDataProvider(): iterable
    {
        yield 'two names' => ['A|B'];
        yield 'three names' => ['A|B|C'];
        yield 'ten names' => ['A|B|C|D|E|F|G|H|I|J'];
        yield 'qualified names' => ['\\Foo\\Bar|Foo\\Baz'];
        yield 'virtual names' => ['non-empty-string|callable-string'];
        yield 'keywords' => ['is|of'];
        yield 'literals' => ['1|2'];
        yield 'negative literals' => ['-1|-2'];
        yield 'strings' => ["'a'|\"b\""];
        yield 'null' => ['null|true|false'];
        yield 'this' => ['$this|A'];
        yield 'class constants' => ['Foo::A|Foo::B'];
        yield 'class constant masks' => ['Foo::A_*|Foo::*'];
        yield 'global masks' => ['PHP_*|E_*'];
        yield 'mask of a namespace' => ['Path\\To\\*|A'];

        // The suffixes are tighter
        yield 'lists' => ['A[]|B[]'];
        yield 'list on the left' => ['A[]|B'];
        yield 'list on the right' => ['A|B[]'];
        yield 'accesses' => ['A[K]|B[L]'];
        yield 'access on the left' => ['A[K]|B'];
        yield 'access on the right' => ['A|B[K]'];
        yield 'chains' => ['A[][K]|B[K][]'];

        // The ampersand is tighter
        yield 'intersection on the left' => ['A&B|C'];
        yield 'intersection on the right' => ['A|B&C'];
        yield 'intersections' => ['A&B|C&D'];
        yield 'intersection in the middle' => ['A|B&C|D'];
        yield 'long intersection' => ['A&B&C&D|E'];
        yield 'intersections with suffixes' => ['A[]&B[K]|C[]&D'];

        // The question mark in front reaches over the whole union
        yield 'nullable in front' => ['?A|B'];
        yield 'nullable in front of three' => ['?A|B|C'];
        yield 'nullable in front of an intersection' => ['?A&B|C'];
        yield 'nullable in front of a list' => ['?A[]|B'];
        yield 'nullable in front of a union with a nullable group' => ['?A|(?B)'];

        // The type behind a bar is any type, the mark reaches over the rest
        yield 'nullable on the right' => ['A|?B'];
        yield 'nullable on the right with a space' => ['A | ? B'];
        yield 'nullable in the middle' => ['A|?B|C'];
        yield 'nullable on the last' => ['A|B|?C'];
        yield 'nullables' => ['A|?B|?C'];
        yield 'nullable list on the right' => ['A|?B[]'];
        yield 'nullable intersection on the right' => ['A|?B&C'];
        yield 'nullable between the intersections' => ['A&B|?C&D'];
        yield 'nullable in a key' => ['T[A|?B]'];

        // A nullable group
        yield 'nullable behind an ampersand' => ['A|B&?C'];
        yield 'nullable group on the right' => ['A|(?B)'];
        yield 'nullable group in the middle' => ['A|(?B)|C'];
        yield 'nullable group, then a nullable' => ['A|(?B)|?C'];
        yield 'nullable group on the last' => ['A|B|(?C)'];
        yield 'nullable groups' => ['A|(?B)|(?C)'];
        yield 'nullable list group on the right' => ['A|(?B[])'];
        yield 'nullable group in an intersection on the right' => ['A|(?B)&C'];
        yield 'nullable group as a member of an intersection' => ['A|B&(?C)'];
        yield 'nullable groups in an intersection' => ['A|(?B)&(?C)'];
        yield 'nullable group between the intersections' => ['A&B|(?C)&D'];

        // Groups
        yield 'group' => ['(A|B)'];
        yield 'group on the left' => ['(A)|B'];
        yield 'group on the right' => ['A|(B)'];
        yield 'groups on both sides' => ['(A)|(B)'];
        yield 'group of a union on the left' => ['(A|B)|C'];
        yield 'group of a union on the right' => ['A|(B|C)'];
        yield 'group of a nullable' => ['(?A)|B'];
        yield 'group of a union in a nullable' => ['?(A|B)'];
        yield 'group of a union in an intersection' => ['(A|B)&C'];
        yield 'group of a union on the right of an intersection' => ['A&(B|C)'];
        yield 'group of a union in a list' => ['(A|B)[]'];
        yield 'group of a union in a list of lists' => ['(A[]|B[])[]'];
        yield 'group of a union in an access' => ['(A|B)[K]'];
        yield 'group of an intersection' => ['(A&B)|C'];
        yield 'nullable group of a union' => ['(?A|B)[]'];
        yield 'groups in groups' => ['((A|B)|(C|D))'];

        // A union as a key
        yield 'union in a key' => ['T[A|B]'];
        yield 'union in a key behind a key' => ['T[A|B][C|D]'];
        yield 'union in a key with a list' => ['T[A|B[]]'];
        yield 'union in a key with an intersection' => ['T[A&B|C]'];
        yield 'nullable union in a key' => ['T[?A|B]'];
        yield 'union in a key of a key' => ['T[K[A|B]]'];
        yield 'union of keys' => ['T[A]|U[B]'];

        // Trivia
        yield 'space before' => ['A |B'];
        yield 'space behind' => ['A| B'];
        yield 'spaces around' => ['A | B'];
        yield 'many spaces' => ['A     |     B'];
        yield 'tabs' => ["A\t|\tB"];
        yield 'line breaks' => ["A\n|\nB"];
        yield 'carriage returns' => ["A\r\n|\r\nB"];
        yield 'line break before' => ["A\n|B"];
        yield 'line break behind' => ["A|\nB"];
        yield 'block comments' => ['A/* a */|/* b */B'];
        yield 'line comments' => ["A // a\n| // b\nB"];
        yield 'hash comments' => ["A # a\n| # b\nB"];
        yield 'spaces around everything' => ['  A  |  B  '];
        yield 'comments around everything' => ['/* a */ A /* b */ | /* c */ B /* d */'];
        yield 'comment behind' => ['A|B // c'];
        yield 'spaces inside of a key' => ['T[ A | B ]'];
        yield 'spaces everywhere' => ['? A [ ] | ? B [ K ] & C | ( D )'];
    }

    #[DataProvider('validUnionDataProvider')]
    public function testUnionIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidUnionDataProvider(): iterable
    {
        yield 'bar alone' => ['|'];
        yield 'bars alone' => ['||'];
        yield 'bar before' => ['|A'];
        yield 'bar behind' => ['A|'];
        yield 'bar behind with a space' => ['A |'];
        yield 'bar behind with a comment' => ['A| /* c */'];
        yield 'bar before with a space' => ['| A'];
        yield 'two bars' => ['A||B'];
        yield 'two bars with a space' => ['A| |B'];
        yield 'two bars with a space between everything' => ['A | | B'];
        yield 'three bars' => ['A|||B'];
        yield 'bar behind the second type' => ['A|B|'];
        yield 'bar before the first type' => ['|A|B'];
        yield 'bar in the empty group' => ['(|)'];
        yield 'bar in the group before' => ['(|A)'];
        yield 'bar in the group behind' => ['(A|)'];
        yield 'bar in the key before' => ['T[|A]'];
        yield 'bar in the key behind' => ['T[A|]'];
        yield 'bar in the empty key' => ['T[|]'];
        yield 'bar after a mark' => ['?|A'];
        yield 'bar behind a mark' => ['A|?'];
        yield 'bar in front of a mark' => ['A?|B'];
        yield 'bar and an ampersand' => ['A|&B'];
        yield 'ampersand and a bar' => ['A&|B'];
        yield 'three marks in front' => ['???A|B'];
        yield 'two marks behind a bar' => ['A|??B'];
        yield 'two marks with a space behind a bar' => ['A| ? ?B'];
        yield 'mark behind a type' => ['A?|B'];
        yield 'bar glued to the brackets' => ['A|[]'];
        yield 'bar in a list' => ['A[|]'];
        yield 'bar behind the brackets' => ['A[]|'];
        yield 'bar with a colon' => ['A|:B'];
        yield 'bar with a star' => ['A|*'];
        yield 'type behind' => ['A|B C'];
        yield 'list behind the group' => ['A|B[] (C)'];
        yield 'type in front' => ['A B|C'];
        yield 'a comma' => ['A|B,C'];
        yield 'comment swallows the second type' => ['A| // B'];
        yield 'bar in a comment only' => ['A /* | */ B'];
        yield 'not closed group' => ['(A|B'];
        yield 'not opened group' => ['A|B)'];
        yield 'not closed key' => ['T[A|B'];
        yield 'not opened key' => ['TA|B]'];
        yield 'mark in front of a bar only' => ['?|'];
    }

    #[DataProvider('invalidUnionDataProvider')]
    public function testUnionIsRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{int<2, max>}>
     */
    public static function countDataProvider(): iterable
    {
        yield 'two' => [2];
        yield 'ten' => [10];
        yield 'one hundred' => [100];
        yield 'one thousand' => [1000];
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyTypesAreAccepted(int $count): void
    {
        self::assertAccepted(\implode('|', \array_fill(0, $count, 'A')));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyTypesWithADanglingBarAreRejected(int $count): void
    {
        self::assertRejected(\implode('|', \array_fill(0, $count, 'A')) . '|');
        self::assertRejected('|' . \implode('|', \array_fill(0, $count, 'A')));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyIntersectionsInAUnionAreAccepted(int $count): void
    {
        self::assertAccepted(\implode('|', \array_fill(0, $count, 'A&B&C')));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyNestedGroupsOfUnionsAreAccepted(int $count): void
    {
        $depth = \min($count, 200);

        self::assertAccepted(\str_repeat('(A|', $depth) . 'B' . \str_repeat(')', $depth));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyNullableMembersAreAccepted(int $count): void
    {
        self::assertAccepted(\implode('|', \array_fill(0, $count, '?A')));
    }
}
