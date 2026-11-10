<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The input a type cannot be read out of: the one that is empty, the one that
 * is cut short, the one that is punctuation alone and the one that has
 * something left behind a whole type.
 */
final class MalformedInputTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function emptyInputDataProvider(): iterable
    {
        yield 'nothing at all' => [''];
        yield 'spaces' => ['   '];
        yield 'a line terminator' => ["\n"];
        yield 'a tabulation' => ["\t"];
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function truncatedInputDataProvider(): iterable
    {
        yield 'a condition without its comparand' => ['A is'];
        yield 'a dangling union' => ['int|'];
        yield 'a dangling intersection' => ['int&'];
        yield 'a question mark alone' => ['?'];
        yield 'a name that is a separator short' => ['Some\\'];
        yield 'a class constant that is a name short' => ['Some::'];
        yield 'an unclosed shape' => ['array{'];
        yield 'an unclosed field' => ['array{a:'];
        yield 'an unclosed argument list' => ['Some<'];
        yield 'an unclosed parameter list' => ['callable('];
        yield 'an unclosed offset' => ['int['];
        yield 'an unclosed group' => ['(int'];
        yield 'a condition without its branches' => ['A is B ?'];
        yield 'a condition of a single branch' => ['A is B ? C'];
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function strayTokenDataProvider(): iterable
    {
        yield 'a union delimiter' => ['|'];
        yield 'an intersection delimiter' => ['&'];
        yield 'a comma' => [','];
        yield 'a colon' => [':'];
        yield 'a double colon' => ['::CONST'];
        yield 'a closing brace' => ['}'];
        yield 'a closing bracket' => [']'];
        yield 'a closing parenthesis' => [')'];
        yield 'an ellipsis' => ['...'];
        yield 'an assignment' => ['='];
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function trailingTokenDataProvider(): iterable
    {
        yield 'a question mark behind a type' => ['int?'];
        yield 'an angle bracket too many' => ['Some<int>>'];
        yield 'a brace too many' => ['array{a: int}}'];
        yield 'a bracket too many' => ['int[]]'];
        yield 'an empty argument list' => ['Some<>'];
        yield 'an empty group' => ['()'];
        yield 'a constant of a generic type' => ['Some<T>::CONST'];
        yield 'a second type' => ['int string'];
        yield 'several words' => ['int and more text'];
    }

    #[DataProvider('emptyInputDataProvider')]
    #[DataProvider('truncatedInputDataProvider')]
    #[DataProvider('strayTokenDataProvider')]
    #[DataProvider('trailingTokenDataProvider')]
    public function testMalformedInputIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
