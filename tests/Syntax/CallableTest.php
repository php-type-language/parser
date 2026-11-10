<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Callable types: parameters, defaults and return types (grammar/callable.pp3).
 */
final class CallableTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validCallableDataProvider(): iterable
    {
        yield 'no parameters' => ['callable(): void'];
        yield 'no return type' => ['callable(int)'];
        yield 'no parameters and no return type' => ['callable()'];
        yield 'one parameter' => ['callable(int): void'];
        yield 'space before the colon' => ['callable(int) : void'];
        yield 'space behind the colon' => ['callable(int):void'];
        yield 'trailing comma' => ['callable(int,): void'];
        yield 'two parameters' => ['callable(int, string): void'];
        yield 'closure' => ['Closure(int): void'];
        yield 'fully qualified closure' => ['\\Closure(int): void'];
        yield 'qualified name' => ['Foo\\Bar(int): void'];
        yield 'pure callable' => ['pure-callable(int): int'];
        yield 'pure closure' => ['pure-Closure(int): int'];
        yield 'any name' => ['foo(T): void'];
        yield 'keyword name' => ['int(T): void'];
        yield 'trivia inside' => ['callable( /* c */ int /* c */ , /* c */ string /* c */ ) /* c */ : /* c */ void'];

        // A gap before the parenthesis is allowed, as PHPStan and Psalm do
        yield 'space before the parenthesis' => ['callable (int): void'];
        yield 'line break before the parenthesis' => ["callable\n(int): void"];
        yield 'comment before the parenthesis' => ['callable/* c */(int): void'];
        yield 'space before the parenthesis of a closure' => ['Closure (int): void'];
        yield 'space before the parenthesis of an empty list' => ['callable (): void'];

        yield 'named parameter' => ['callable(int $a): void'];
        yield 'name only' => ['callable($a): void'];
        yield 'this as a type' => ['callable($this): void'];
        yield 'by reference' => ['callable(int &$a): void'];
        yield 'by reference without a name' => ['callable(int&): void'];
        yield 'by reference without a type' => ['callable(&$a): void'];
        yield 'by reference of a name' => ['callable(A &$x): void'];
        yield 'intersection as a type' => ['callable(A&B $x): void'];
        yield 'intersection as a type of a reference' => ['callable(A&B &$x): void'];

        yield 'variadic' => ['callable(int...): void'];
        yield 'variadic with a space' => ['callable(int ...): void'];
        yield 'variadic with a name' => ['callable(int ...$a): void'];
        yield 'variadic with a name glued to a type' => ['callable(int... $a): void'];
        yield 'variadic by reference' => ['callable(int &...$a): void'];
        yield 'variadic is not the last' => ['callable(int..., string): void'];
        yield 'variadic in front of a type' => ['callable(...int): void'];
        yield 'variadic in front of a name' => ['callable(...$a): void'];
        yield 'variadic in front of a name by reference' => ['callable(&...$a): void'];

        yield 'optional' => ['callable(int=): void'];
        yield 'optional with a name' => ['callable(int $a=): void'];
        yield 'optional by reference' => ['callable(int &$a=): void'];
        yield 'optional before a required' => ['callable(int=, string): void'];
        yield 'optional with a name only' => ['callable($a=): void'];
        yield 'default integer' => ['callable(int $a = 42): void'];
        yield 'default negative integer' => ['callable(int $a = -1): void'];
        yield 'default negative integer with a gap' => ['callable(int $a = - 1): void'];
        yield 'default float' => ['callable(float $a = 1.5): void'];
        yield 'default string' => ["callable(string \$a = 'x'): void"];
        yield 'default true' => ['callable(bool $a = true): void'];
        yield 'default null' => ['callable(?int $a = null): void'];
        yield 'default class constant' => ['callable(int $a = Foo::BAR): void'];
        yield 'default class name' => ['callable(string $a = Foo::class): void'];
        yield 'default global constant' => ['callable(int $a = PHP_INT_MAX): void'];
        yield 'default fully qualified constant' => ['callable(string $a = \\PHP_EOL): void'];
        yield 'default without a type' => ['callable($a = 42): void'];
        yield 'default without a name' => ['callable(int = 42): void'];
        yield 'default by reference' => ['callable(int &$a = 42): void'];
        yield 'defaults of two' => ['callable(int $a = 42, string $b = "x",): void'];
        yield 'default and a required one' => ['callable(int $a = 42, string $b): void'];

        yield 'return union' => ['callable(): int|string'];
        yield 'return nullable' => ['callable(): ?int'];
        yield 'return list' => ['callable(): int[]'];
        yield 'return offset' => ['callable(): T[K]'];
        yield 'return generic' => ['callable(): array<int, string>'];
        yield 'return shape' => ['callable(): array{a: int}'];
        yield 'return this' => ['callable(): $this'];
        yield 'return static' => ['callable(): static'];
        yield 'return grouped' => ['callable(): (int|string)'];
        yield 'return callable' => ['callable(): callable(): void'];
        yield 'return literal' => ['callable(): 42'];
        yield 'return class constant' => ['callable(): Foo::BAR'];

        yield 'shape parameters' => ['callable(array{a: int} $a, list<int> ...$b): array{b: string}'];
        yield 'callable as a parameter' => ['callable(callable(int): void $cb): void'];
        yield 'callable as a parameter with no name' => ['callable(callable(int): void): void'];
        yield 'callable as an argument' => ['Foo<callable(int): void>'];
        yield 'callable as a shape field' => ['array{a: callable(int): void, b: int}'];
        yield 'callable as an implicit shape field' => ['array{callable(int): void}'];
        yield 'nullable callable' => ['?callable(): int'];
        yield 'list of callables' => ['(callable(): int)[]'];
        yield 'callable in a union' => ['callable(): int|string'];
        yield 'callable behind a union' => ['int|callable(): void'];
        yield 'callable in an intersection' => ['A&callable(): void'];
    }

    #[DataProvider('validCallableDataProvider')]
    public function testCallableIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidCallableDataProvider(): iterable
    {
        yield 'unclosed' => ['callable('];
        yield 'unclosed after a parameter' => ['callable(int'];
        yield 'unclosed after a comma' => ['callable(int,'];
        yield 'no return type behind a colon' => ['callable(int):'];
        yield 'only a colon' => ['callable(): '];
        yield 'extra parenthesis' => ['callable(int)) void'];
        yield 'extra colon' => ['callable(int):: void'];
        yield 'name behind the default marker' => ['callable(int= $a): void'];
        yield 'only a comma' => ['callable(,)'];
        yield 'empty parameter' => ['callable(int,,)'];
        yield 'empty parameter in front' => ['callable(,int)'];
        yield 'colon in front of the parentheses' => ['callable: (int)'];
        yield 'return type in the list' => ['callable(int: void)'];

        yield 'variadic alone' => ['callable(...): void'];
        yield 'variadic alone behind a comma' => ['callable(int, ...): void'];
        yield 'variadic behind a name' => ['callable(int $a...): void'];
        yield 'variadic on both sides' => ['callable(...int...): void'];
        yield 'by reference before a type' => ['callable(&int): void'];
        yield 'by reference behind a name' => ['callable(int $a&): void'];
        yield 'variadic behind by reference' => ['callable(int ...&): void'];
        yield 'variadic behind by reference with a name' => ['callable(int ...&$a): void'];
        yield 'by reference alone' => ['callable(&): void'];
        yield 'no name behind a dollar' => ['callable(int $): void'];
        yield 'this as a name' => ['callable(int $this): void'];
        yield 'two names' => ['callable(int $a $b): void'];
        yield 'two types and a name' => ['callable(int string $a): void'];

        yield 'optional before a type' => ['callable(=int): void'];
        yield 'optional alone' => ['callable(= 42): void'];
        yield 'optional behind a variadic marker' => ['callable(int=...): void'];
        yield 'default of a variadic' => ['callable(int ...$a = 1): void'];
        yield 'optional variadic' => ['callable(int ...=): void'];
        yield 'optional variadic with a name' => ['callable(int ...$a=): void'];
        yield 'default is an expression' => ['callable(int $a = 1 + 2): void'];
        yield 'default is an array' => ['callable(array $a = []): void'];
        yield 'default is a variable' => ['callable(int $a = $b): void'];
        yield 'default is a call' => ['callable(int $a = foo()): void'];
        yield 'default is a union' => ['callable(int $a = Foo::A|Foo::B): void'];
        yield 'default is a mask' => ['callable(int $a = Foo::A_*): void'];
        yield 'default is a list' => ['callable(int $a = Foo::A[]): void'];
        yield 'double assignment' => ['callable(int $a == 42): void'];
        yield 'two defaults' => ['callable(int $a = 1 = 2): void'];

        yield 'return type is a variable' => ['callable(): $a'];
    }

    #[DataProvider('invalidCallableDataProvider')]
    public function testCallableIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
