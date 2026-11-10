<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The suffix `[K]` behind a type (grammar.pp3).
 */
final class OffsetAccessTypeTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validAccessDataProvider(): iterable
    {
        yield 'name' => ['T[K]'];
        yield 'qualified names' => ['\\Foo\\Bar[\\Baz]'];
        yield 'builtin' => ['array[int]'];
        yield 'single quoted key' => ["T['a']"];
        yield 'double quoted key' => ['T["a"]'];
        yield 'int key' => ['T[0]'];
        yield 'negative int key' => ['T[-1]'];
        yield 'hex key' => ['T[0x1F]'];
        yield 'float key' => ['T[1.5]'];
        yield 'bool key' => ['T[true]'];
        yield 'null key' => ['T[null]'];
        yield 'class constant key' => ['T[Foo::A]'];
        yield 'class constant mask key' => ['T[Foo::A_*]'];
        yield 'global mask key' => ['T[PHP_*]'];
        yield 'this key' => ['T[$this]'];
        yield 'list key' => ['T[K[]]'];
        yield 'access key' => ['T[K[L]]'];
        yield 'group key' => ['T[(K)]'];
        yield 'group of a list key' => ['T[(K)[]]'];

        yield 'this' => ['$this[K]'];
        yield 'class constant' => ['Foo::BAR[K]'];
        yield 'class constant mask' => ['Foo::*[K]'];
        yield 'global mask' => ['PHP_*[K]'];
        yield 'literal' => ["'a'[K]"];
        yield 'group' => ['(T)[K]'];
        yield 'group of a list' => ['(T[])[K]'];

        yield 'chain' => ['T[K][L]'];
        yield 'long chain' => ['T[A][B][C][D]'];
        yield 'list behind' => ['T[K][]'];
        yield 'list before' => ['T[][K]'];
        yield 'mix' => ['T[][K][][L]'];

        yield 'space before the bracket' => ['T [K]'];
        yield 'space after the bracket' => ['T[ K]'];
        yield 'space before the closing bracket' => ['T[K ]'];
        yield 'spaces everywhere' => ['T [ K ] [ L ]'];
        yield 'line breaks' => ["T\n[\nK\n]"];
        yield 'carriage returns' => ["T\r\n[\r\nK\r\n]"];
        yield 'block comments' => ['T/* a */[/* b */K/* c */]'];
        yield 'line comment inside' => ["T[ // c\nK]"];
        yield 'line comment behind' => ['T[K] // c'];
    }

    #[DataProvider('validAccessDataProvider')]
    public function testAccessIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidAccessDataProvider(): iterable
    {
        yield 'not closed' => ['T[K'];
        yield 'not closed chain' => ['T[K][L'];
        yield 'not opened' => ['T]'];
        yield 'extra closing bracket' => ['T[K]]'];
        yield 'reversed' => ['T]K['];
        yield 'key alone' => ['[K]'];
        yield 'brackets in brackets' => ['T[[]]'];
        yield 'brackets with a key in brackets' => ['T[[K]]'];
        yield 'two keys' => ['T[K L]'];
        yield 'keys with a comma' => ['T[K, L]'];
        yield 'type behind' => ['T[K] L'];
        yield 'type behind with no space' => ['T[K]L'];
        yield 'key behind' => ['T[]K'];
        yield 'group behind' => ['T[K](L)'];
        yield 'shape behind' => ["T['a']{a: int}"];
        yield 'empty group inside' => ['T[()]'];
        yield 'star inside' => ['T[*]'];
        yield 'colons inside' => ['T[::]'];
        yield 'comment swallows the bracket' => ['T[K // ]'];
        yield 'key in a comment' => ['T[/* K */'];
    }

    #[DataProvider('invalidAccessDataProvider')]
    public function testAccessIsRejected(string $source): void
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
    public function testChainOfAccessesIsAccepted(int $depth): void
    {
        self::assertAccepted('T' . \str_repeat('[K]', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testAccessesInsideOfAccessesAreAccepted(int $depth): void
    {
        self::assertAccepted('T' . \str_repeat('[K', $depth) . \str_repeat(']', $depth));
    }

    /**
     * @param int<1, max> $depth
     */
    #[DataProvider('depthDataProvider')]
    public function testAccessesInsideOfAccessesNotClosedAreRejected(int $depth): void
    {
        self::assertRejected('T' . \str_repeat('[K', $depth) . \str_repeat(']', $depth - 1));
        self::assertRejected('T' . \str_repeat('[K', $depth) . \str_repeat(']', $depth + 1));
    }
}
