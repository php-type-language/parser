<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The variable `$name` (grammar.pp3). It is no type, so it stands as the name of
 * a parameter of a callable and a type never accepts it.
 */
final class VariableTest extends SyntaxTestCase
{
    private static function parameter(string $variable): string
    {
        return 'callable(' . $variable . "\n)";
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validVariableDataProvider(): iterable
    {
        yield 'letter' => ['$a'];
        yield 'upper case letter' => ['$A'];
        yield 'word' => ['$value'];
        yield 'underscore' => ['$_'];
        yield 'two underscores' => ['$__'];
        yield 'underscore in front' => ['$_x'];
        yield 'underscore inside' => ['$a_b'];
        yield 'digit behind' => ['$a1'];
        yield 'digit behind an underscore' => ['$_1'];
        yield 'camel case' => ['$someValue'];
        yield 'unicode letter' => ['$Ф'];
        yield 'byte above ascii' => ["\$\u{FF}"];
        yield 'emoji' => ["\$\u{1F600}"];

        // The word this is a variable only as a part of a longer name
        yield 'this and a suffix' => ['$thisFoo'];
        yield 'this and an underscore' => ['$this_'];
        yield 'this and a letter' => ['$thisx'];
        yield 'this in upper case' => ['$THIS'];
        yield 'this in camel case' => ['$This'];

        // Trivia
        yield 'spaces around' => ['  $a  '];
        yield 'line breaks around' => ["\n\$a\n"];
        yield 'comment in front' => ['/* c */ $a'];
        yield 'comment behind' => ['$a /* c */'];
        yield 'line comment behind' => ['$a // c'];
        yield 'hash comment behind' => ['$a # c'];
    }

    #[DataProvider('validVariableDataProvider')]
    public function testVariableIsAccepted(string $source): void
    {
        self::assertAccepted(self::parameter($source));
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidVariableDataProvider(): iterable
    {
        yield 'dollar alone' => ['$'];
        yield 'two dollars' => ['$$'];
        yield 'variable variable' => ['$$a'];
        yield 'space behind the dollar' => ['$ a'];
        yield 'line break behind the dollar' => ["\$\na"];
        yield 'comment behind the dollar' => ['$/* c */a'];
        yield 'digit in front' => ['$1'];
        yield 'digit in front of a word' => ['$1a'];
        yield 'hyphen in front' => ['$-a'];
        yield 'hyphen inside' => ['$a-b'];
        yield 'dot inside' => ['$a.b'];
        yield 'backslash inside' => ['$a\\b'];
        yield 'dollar behind' => ['a$'];
        yield 'two variables' => ['$a $b'];
        yield 'two variables glued' => ['$a$b'];
        yield 'variable and a name' => ['$a b'];
        yield 'variable and brackets' => ['$a[]'];
        yield 'variable and a key' => ['$a[K]'];
        yield 'variable and a bar' => ['$a|b'];
        yield 'variable and an ampersand' => ['$a&b'];
        yield 'variable and a mark' => ['$a?'];
        yield 'variable and a colon' => ['$a::B'];
        yield 'variable and arguments' => ['$a<int>'];
        yield 'variable and parentheses' => ['$a()'];
    }

    #[DataProvider('invalidVariableDataProvider')]
    public function testVariableIsRejected(string $source): void
    {
        self::assertRejected(self::parameter($source));
    }

    /**
     * The word this is a type of its own, so in the place of a parameter it
     * stands as the type and not as a name.
     */
    public function testThisIsAcceptedAsATypeOfAParameter(): void
    {
        self::assertAccepted(self::parameter('$this'));
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function variableAsATypeDataProvider(): iterable
    {
        yield 'variable' => ['$a'];
        yield 'variable with an underscore' => ['$_x'];
        yield 'variable with a long name' => ['$thisFoo'];
        yield 'nullable variable' => ['?$a'];
        yield 'list of variables' => ['$a[]'];
        yield 'union of variables' => ['$a|$b'];
        yield 'intersection with a variable' => ['A&$b'];
        yield 'variable in a group' => ['($a)'];
        yield 'variable as an argument' => ['Foo<$a>'];
        yield 'variable as a key' => ['array{$a: int}'];
        yield 'variable as a field' => ['array{$a}'];
        yield 'variable as a constant class' => ['$a::BAR'];
        yield 'variable as a shape value' => ['array{a: $a}'];
        yield 'variable as a return type' => ['callable(): $a'];
        yield 'variable as an offset' => ['T[$a]'];
        yield 'this as a constant class' => ['$this::BAR'];
        yield 'variable in front of the type of a parameter' => ['callable($a int): void'];
        yield 'this as a name of a parameter' => ['callable(int $this): void'];
        yield 'variable with a dash as a name of a parameter' => ['callable(int $a-b): void'];
    }

    #[DataProvider('variableAsATypeDataProvider')]
    public function testVariableIsNoType(string $source): void
    {
        self::assertRejected($source);
    }
}
