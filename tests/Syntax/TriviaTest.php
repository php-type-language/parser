<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Whitespaces, comments and what follows a whole type (grammar/lexemes.pp3).
 */
final class TriviaTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validTriviaDataProvider(): iterable
    {
        yield 'spaces around' => ['  int  '];
        yield 'tabs around' => ["\tint\t"];
        yield 'line breaks around' => ["\nint\n"];
        yield 'carriage returns around' => ["\r\nint\r\n"];
        yield 'line breaks around a bar' => ["int\n|\nstring"];
        yield 'line break before a bar' => ["int\n|string"];
        yield 'line break behind a bar' => ["int|\nstring"];
        yield 'tabs around a bar' => ["int\t|\tstring"];
        yield 'space before the double colon' => ['Foo ::BAR'];
        yield 'space behind the double colon' => ['Foo:: BAR'];
        yield 'space before the parenthesis of a callable' => ['callable (int): void'];
        yield 'line break before the parenthesis of a callable' => ["callable\n(int): void"];
        yield 'space before the arguments' => ['Foo <int>'];
        yield 'line break before the arguments' => ["array\n<int>"];
        yield 'space before the shape' => ['array {a: int}'];
        yield 'line break before the shape' => ["array\n{a: int}"];
        yield 'space before the bracket of an offset' => ['T [K]'];

        yield 'arguments on several lines' => ["array<\n    int,\n    string,\n>"];
        yield 'shape on several lines' => ["array{\n    a: int,\n    b?: string,\n}"];
        yield 'callable on several lines' => ["callable(\n    int \$a,\n    string \$b,\n): void"];
        yield 'callable with a break before the colon' => ["callable(int)\n: void"];

        yield 'block comment behind' => ['int /* c */'];
        yield 'block comment before' => ['/* c */ int'];
        yield 'block comment around a bar' => ['int /* c */ | /* c */ string'];
        yield 'documentation comment' => ['/** c */ int'];
        yield 'empty comment' => ['/**/ int'];
        yield 'line comment behind' => ['int // c'];
        yield 'line comment and a continuation' => ["int // c\n| string"];
        yield 'hash comment behind' => ['int # c'];
        yield 'hash comment and a continuation' => ["int # c\n| string"];
        yield 'line comment before' => ["// c\nint"];
        yield 'comment inside a shape' => ['array{a: int /* c */, b: string}'];
        yield 'comment before a field' => ['array{/* c */ a: int}'];
        yield 'comment inside parameters' => ['callable(/* c */ int /* c */): void'];

        yield 'cyrillic name' => ['Фу'];
        yield 'line break inside a string' => ["'a\nb'"];
        yield 'line break after a sign' => ["-\n1"];
        yield 'cyrillic string' => ["'Фу'"];
        yield 'emoji string' => ['"😀"'];
        yield 'combining character' => ["a\u{0310}"];
        yield 'zero width space is a part of a name' => ["int\u{200B}"];
        yield 'byte order mark is a part of a name' => ["\u{FEFF}int"];
    }

    #[DataProvider('validTriviaDataProvider')]
    public function testSourceIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidTriviaDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'space' => [' '];
        yield 'line break' => ["\n"];
        yield 'tab' => ["\t"];
        yield 'only a comment' => ['/* c */'];
        yield 'only a line comment' => ['// c'];

        yield 'unterminated block comment' => ['/* int'];
        yield 'block comment end alone' => ['int */'];
        yield 'star alone' => ['int *'];
        yield 'space inside a word' => ['in t'];
        yield 'space inside a name' => ['i nt'];
        yield 'space inside a namespace' => ['Foo \\ Bar'];
        yield 'comment inside a namespace' => ['Some/* c */\\Any'];
        yield 'comment glued to a namespace' => ['Some\\/* c */Any'];
    }

    #[DataProvider('invalidTriviaDataProvider')]
    public function testSourceIsRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function trailingInputDataProvider(): iterable
    {
        yield 'a second name' => ['int string'];
        yield 'a comma' => ['int, string'];
        yield 'a semicolon' => ['int;'];
        yield 'a semicolon and a name' => ['int;string'];
        yield 'a name and a bar' => ['int string|bool'];
        yield 'a variable' => ['int $a'];
        yield 'parameters' => ['int $a, string $b'];
        yield 'a variadic variable' => ['int ...$a'];
        yield 'a variadic marker' => ['int...'];
        yield 'a variadic marker before' => ['...int'];
        yield 'a dot' => ['int.'];
        yield 'a leading dot' => ['.int'];
        yield 'a trailing comma' => ['int,'];
        yield 'a leading comma' => [',int'];
        yield 'a closing parenthesis' => ['int)'];
        yield 'an opening parenthesis' => ['(int'];
        yield 'a closing bracket' => ['int]'];
        yield 'an opening bracket' => ['[int'];
        yield 'a closing brace' => ['int}'];
        yield 'an opening brace' => ['{int'];
        yield 'a closing angle bracket' => ['int>'];
        yield 'an opening angle bracket' => ['<int'];
        yield 'a bar behind a closing parenthesis' => ['int|string)'];
        yield 'an unclosed group behind a bar' => ['(int|string'];
        yield 'at sign' => ['int@'];
        yield 'at sign before' => ['@int'];
        yield 'exclamation mark' => ['int!'];
        yield 'tilde' => ['int~'];
        yield 'caret' => ['int^'];
        yield 'percent' => ['int%'];
        yield 'assignment' => ['int='];
        yield 'assignment before' => ['=int'];
        yield 'backslash behind' => ['int\\'];
        yield 'backquote' => ['int`'];
        yield 'slash' => ['int/'];
        yield 'plus' => ['int+'];
        yield 'colon' => ['int:'];
        yield 'colon before' => [':int'];
        yield 'double colon' => ['int::'];
        yield 'double colon before' => ['::int'];
        yield 'question mark and a name' => ['int ? a'];
        yield 'two question marks' => ['int??'];
    }

    #[DataProvider('trailingInputDataProvider')]
    public function testTrailingInputIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
