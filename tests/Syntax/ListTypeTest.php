<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The suffix `[]` behind a type (grammar.pp3).
 */
final class ListTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validListDataProvider(): iterable
    {
        yield 'name' => ['int[]'];
        yield 'qualified name' => ['\\Foo\\Bar[]'];
        yield 'virtual name' => ['non-empty-string[]'];
        yield 'keyword' => ['is[]'];
        yield 'literal word' => ['null[]'];
        yield 'bool literal' => ['true[]'];
        yield 'int literal' => ['42[]'];
        yield 'float literal' => ['1.5[]'];
        yield 'string literal' => ["'text'[]"];
        yield 'this' => ['$this[]'];
        yield 'class constant' => ['Foo::BAR[]'];
        yield 'class constant mask' => ['Foo::BAR_*[]'];
        yield 'star of a class' => ['Foo::*[]'];
        yield 'global mask' => ['PHP_*[]'];
        yield 'mask of a namespace' => ['Path\\To\\*[]'];
        yield 'group' => ['(int)[]'];
        yield 'group of a list' => ['(int[])[]'];
        yield 'two lists' => ['int[][]'];
        yield 'five lists' => ['int[][][][][]'];
        yield 'group and lists' => ['(A)[][]'];

        yield 'space before' => ['int []'];
        yield 'space inside' => ['int[ ]'];
        yield 'spaces around' => ['int [ ]'];
        yield 'space between the lists' => ['int[] []'];
        yield 'spaces between everything' => ['int [ ] [ ]'];
        yield 'line break before' => ["int\n[]"];
        yield 'line break inside' => ["int[\n]"];
        yield 'line breaks around' => ["int\r\n[\r\n]\r\n"];
        yield 'block comment before' => ['int/* c */[]'];
        yield 'block comment inside' => ['int[/* c */]'];
        yield 'line comment before' => ["int // c\n[]"];
        yield 'comment after the list' => ['int[] // c'];
        yield 'space behind a group' => ['(int) []'];
        yield 'brackets in a comment only' => ['int /* [] */'];
    }

    #[DataProvider('validListDataProvider')]
    public function testListIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidListDataProvider(): iterable
    {
        yield 'brackets alone' => ['[]'];
        yield 'not closed' => ['int['];
        yield 'not opened' => ['int]'];
        yield 'reversed' => ['int]['];
        yield 'nested brackets' => ['int[[]]'];
        yield 'words inside' => ['int[ a b ]'];
        yield 'type behind' => ['int[] string'];
        yield 'type behind with no space' => ['int[]string'];
        yield 'two types' => ['int string[]'];
        yield 'group behind' => ['int[](string)'];
        yield 'empty group' => ['()[]'];
        yield 'star alone' => ['*[]'];
        yield 'comment swallows the bracket' => ['int[ // ]'];
    }

    #[DataProvider('invalidListDataProvider')]
    public function testListIsRejected(string $source): void
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
    public function testChainOfListsIsAccepted(int $depth): void
    {
        self::assertAccepted('int' . \str_repeat('[]', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testChainOfListsNotClosedIsRejected(int $depth): void
    {
        self::assertRejected('int' . \str_repeat('[]', $depth - 1) . '[');
        self::assertRejected('int' . \str_repeat('[]', $depth - 1) . ']');
    }
}
