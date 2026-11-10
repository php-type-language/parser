<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The parentheses around a type (grammar.pp3).
 */
final class GroupTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validGroupDataProvider(): iterable
    {
        yield 'name' => ['(int)'];
        yield 'two pairs' => ['((int))'];
        yield 'three pairs' => ['(((int)))'];
        yield 'qualified name' => ['(\\Foo\\Bar)'];
        yield 'virtual name' => ['(non-empty-string)'];
        yield 'literal' => ['(42)'];
        yield 'string literal' => ["('text')"];
        yield 'this' => ['($this)'];
        yield 'class constant' => ['(Foo::BAR)'];
        yield 'class constant mask' => ['(Foo::BAR_*)'];
        yield 'global mask' => ['(PHP_*)'];
        yield 'spaces inside' => ['(  int  )'];
        yield 'spaces around' => ['  ( int )  '];
        yield 'line breaks inside' => ["(\n    int\n)"];
        yield 'comment inside' => ['( /* c */ int /* c */ )'];
        yield 'comment behind' => ['(int) // c'];
        yield 'spaces between the pairs' => ['( ( int ) )'];
        yield 'comment between the pairs' => ['(/* c */(int))'];
    }

    #[DataProvider('validGroupDataProvider')]
    public function testGroupIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidGroupDataProvider(): iterable
    {
        yield 'empty' => ['()'];
        yield 'empty with spaces' => ['(  )'];
        yield 'empty pair inside' => ['(())'];
        yield 'empty pair in a pair' => ['( () )'];
        yield 'not closed' => ['(int'];
        yield 'not closed pair in a pair' => ['((int)'];
        yield 'not opened' => ['int)'];
        yield 'not opened pair in a pair' => ['(int))'];
        yield 'open alone' => ['('];
        yield 'close alone' => [')'];
        yield 'reversed' => [')int('];
        yield 'two types' => ['(int string)'];
        yield 'two groups' => ['(int) (string)'];
        yield 'two groups with no space' => ['(int)(string)'];
        yield 'list and group' => ['int[] (string)'];
        yield 'literal and group' => ['42 (string)'];
        yield 'group and type' => ['(int) string'];
        yield 'comment swallows the bracket' => ['(int // )'];
        yield 'bracket in a comment only' => ['(int /* ) */'];
    }

    #[DataProvider('invalidGroupDataProvider')]
    public function testGroupIsRejected(string $source): void
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
    public function testNestedGroupsAreAccepted(int $depth): void
    {
        self::assertAccepted(\str_repeat('(', $depth) . 'int' . \str_repeat(')', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testNestedGroupsNotClosedAreRejected(int $depth): void
    {
        self::assertRejected(\str_repeat('(', $depth) . 'int' . \str_repeat(')', $depth - 1));
        self::assertRejected(\str_repeat('(', $depth - 1) . 'int' . \str_repeat(')', $depth));
    }
}
