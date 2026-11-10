<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The types joined with the ampersand (grammar.pp3).
 */
final class IntersectionTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validIntersectionDataProvider(): iterable
    {
        yield 'two names' => ['A&B'];
        yield 'three names' => ['A&B&C'];
        yield 'ten names' => ['A&B&C&D&E&F&G&H&I&J'];
        yield 'qualified names' => ['\\Foo\\Bar&Foo\\Baz'];
        yield 'virtual names' => ['non-empty-string&callable-string'];
        yield 'keywords' => ['is&of'];
        yield 'literals' => ['1&2'];
        yield 'strings' => ["'a'&'b'"];
        yield 'null' => ['null&true'];
        yield 'this' => ['$this&A'];
        yield 'class constants' => ['Foo::A&Foo::B'];
        yield 'class constant masks' => ['Foo::A_*&Foo::*'];
        yield 'global masks' => ['PHP_*&E_*'];
        yield 'mask of a namespace' => ['Path\\To\\*&A'];

        yield 'lists' => ['A[]&B[]'];
        yield 'list on the left' => ['A[]&B'];
        yield 'list on the right' => ['A&B[]'];
        yield 'accesses' => ['A[K]&B[L]'];
        yield 'access on the left' => ['A[K]&B'];
        yield 'chains' => ['A[][K]&B[K][]'];

        yield 'nullable on the left' => ['?A&B'];
        // The mark behind an ampersand reaches over the rest of the type
        yield 'nullable on the right' => ['A&?B'];
        yield 'nullable on the right with a space' => ['A & ? B'];
        yield 'nullable in the middle' => ['A&?B&C'];
        yield 'nullable on the last' => ['A&B&?C'];
        yield 'nullables' => ['A&?B&?C'];
        yield 'nullable list on the right' => ['A&?B[]'];
        yield 'nullable union on the right' => ['A&?B|C'];
        yield 'nullable in a key' => ['T[A&?B]'];
        yield 'nullable in a group on the right' => ['A&(?B)'];
        yield 'nullables in groups on both sides' => ['(?A)&(?B)'];
        yield 'nullable in a group in the middle' => ['A&(?B)&C'];
        yield 'nullable in front of a nullable list' => ['?A[]&(?B[K])'];

        yield 'group' => ['(A&B)'];
        yield 'group on the left' => ['(A)&B'];
        yield 'group on the right' => ['A&(B)'];
        yield 'groups on both sides' => ['(A)&(B)'];
        yield 'group of an intersection on the left' => ['(A&B)&C'];
        yield 'group of an intersection on the right' => ['A&(B&C)'];
        yield 'group of a nullable' => ['(?A)&B'];
        yield 'nullable of a group' => ['?(A&B)'];
        yield 'group of an intersection in a list' => ['(A&B)[]'];
        yield 'group of an intersection in a key' => ['T[A&B]'];
        yield 'intersection in a key with a nullable' => ['T[?A&B]'];
        yield 'intersection in a key in a key' => ['T[K[A&B]]'];
        yield 'intersection in groups' => ['((A&B)&(C&D))'];

        yield 'space before' => ['A &B'];
        yield 'space behind' => ['A& B'];
        yield 'spaces around' => ['A & B'];
        yield 'many spaces' => ['A     &     B'];
        yield 'tabs' => ["A\t&\tB"];
        yield 'line breaks' => ["A\n&\nB"];
        yield 'carriage returns' => ["A\r\n&\r\nB"];
        yield 'line break before' => ["A\n&B"];
        yield 'line break behind' => ["A&\nB"];
        yield 'block comments' => ['A/* a */&/* b */B'];
        yield 'line comments' => ["A // a\n& // b\nB"];
        yield 'hash comments' => ["A # a\n& # b\nB"];
        yield 'spaces around everything' => ['  A  &  B  '];
        yield 'comments around everything' => ['/* a */ A /* b */ & /* c */ B /* d */'];
        yield 'comment behind' => ['A&B // c'];
        yield 'spaces inside of a key' => ['T[ A & B ]'];
        yield 'spaces everywhere' => ['? A [ ] & B [ K ] & ( ? C )'];
    }

    #[DataProvider('validIntersectionDataProvider')]
    public function testIntersectionIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidIntersectionDataProvider(): iterable
    {
        yield 'ampersand alone' => ['&'];
        yield 'ampersands alone' => ['&&'];
        yield 'ampersand before' => ['&A'];
        yield 'ampersand behind' => ['A&'];
        yield 'ampersand behind with a space' => ['A &'];
        yield 'ampersand behind with a comment' => ['A& /* c */'];
        yield 'ampersand before with a space' => ['& A'];
        yield 'two ampersands' => ['A&&B'];
        yield 'two ampersands with a space' => ['A& &B'];
        yield 'two ampersands with a space between everything' => ['A & & B'];
        yield 'three ampersands' => ['A&&&B'];
        yield 'ampersand behind the second type' => ['A&B&'];
        yield 'ampersand before the first type' => ['&A&B'];
        yield 'ampersand in the empty group' => ['(&)'];
        yield 'ampersand in the group before' => ['(&A)'];
        yield 'ampersand in the group behind' => ['(A&)'];
        yield 'ampersand in the key before' => ['T[&A]'];
        yield 'ampersand in the key behind' => ['T[A&]'];
        yield 'ampersand in the empty key' => ['T[&]'];
        yield 'ampersand after a mark' => ['?&A'];
        yield 'ampersand behind a mark' => ['A&?'];
        yield 'ampersand in front of a mark' => ['A?&B'];
        yield 'two marks' => ['A&??B'];
        yield 'ampersand glued to the brackets' => ['A&[]'];
        yield 'ampersand in a list' => ['A[&]'];
        yield 'ampersand behind the brackets' => ['A[]&'];
        yield 'ampersand with a colon' => ['A&:B'];
        yield 'ampersand with a star' => ['A&*'];
        yield 'type behind' => ['A&B C'];
        yield 'list behind the group' => ['A&B[] (C)'];
        yield 'type in front' => ['A B&C'];
        yield 'a comma' => ['A&B,C'];
        yield 'comment swallows the second type' => ['A& // B'];
        yield 'ampersand in a comment only' => ['A /* & */ B'];
        yield 'not closed group' => ['(A&B'];
        yield 'not opened group' => ['A&B)'];
        yield 'not closed key' => ['T[A&B'];
        yield 'not opened key' => ['TA&B]'];
    }

    #[DataProvider('invalidIntersectionDataProvider')]
    public function testIntersectionIsRejected(string $source): void
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
        self::assertAccepted(\implode('&', \array_fill(0, $count, 'A')));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyTypesWithADanglingAmpersandAreRejected(int $count): void
    {
        self::assertRejected(\implode('&', \array_fill(0, $count, 'A')) . '&');
        self::assertRejected('&' . \implode('&', \array_fill(0, $count, 'A')));
    }

    /**
     * @param int<2, max> $count
     */
    #[DataProvider('countDataProvider')]
    public function testManyNestedGroupsOfIntersectionsAreAccepted(int $count): void
    {
        $depth = \min($count, 200);

        self::assertAccepted(\str_repeat('(A&', $depth) . 'B' . \str_repeat(')', $depth));
    }
}
