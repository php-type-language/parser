<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Arguments and parameters of generics (grammar/templates.pp3).
 */
final class TemplatesTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validArgumentsDataProvider(): iterable
    {
        yield 'one argument' => ['Foo<int>'];
        yield 'two arguments' => ['Foo<int, string>'];
        yield 'without spaces' => ['Foo<int,string>'];
        yield 'with spaces inside' => ['Foo< int , string >'];
        yield 'space before the bracket' => ['Foo <int>'];
        yield 'line break before the bracket' => ["Foo\n<int>"];
        yield 'comment before the bracket' => ['Foo/* c */<int>'];
        yield 'trailing comma' => ['Foo<int, string,>'];
        yield 'nested' => ['Foo<Bar<Baz<int>>>'];
        yield 'namespaced' => ['\\Foo\\Bar<int>'];

        yield 'wildcard' => ['Foo<*>'];
        yield 'wildcard behind an argument' => ['Foo<int, *>'];
        yield 'covariant' => ['Foo<covariant T>'];
        yield 'contravariant and covariant' => ['Foo<contravariant T, covariant U>'];
        yield 'invariant' => ['Foo<invariant T>'];
        yield 'variance and a wildcard' => ['Foo<covariant A, contravariant B, *>'];
        yield 'variance and a group' => ['Foo<covariant (A|B)>'];
        yield 'variance and a group with no space' => ['Foo<covariant(A|B)>'];
        yield 'callable is not a variance' => ['Foo<callable(int): void>'];
        yield 'any other word before a group is a callable' => ['Foo<in (A|B)>'];
        yield 'variance word as a name' => ['Foo<covariant>'];
        yield 'variance word as a name behind a comma' => ['Foo<A, invariant>'];
        yield 'variance word as a type of a variance' => ['Foo<covariant covariant>'];

        yield 'keyword is' => ['Foo<is>'];
        yield 'keyword not' => ['Foo<not>'];
        yield 'keywords of, as, super' => ['Foo<of, as, super>'];
        yield 'condition' => ['Foo<A is B ? C : D>'];
        yield 'condition and an argument' => ['Foo<T is int ? A : B, C>'];

        yield 'union' => ['Foo<int|string>'];
        yield 'intersection' => ['Foo<A&B>'];
        yield 'grouped union' => ['Foo<(A|B)>'];
        yield 'nullable' => ['Foo<?int>'];
        yield 'list' => ['Foo<int[]>'];
        yield 'callable' => ['Foo<callable(): int>'];
        yield 'callable and an argument' => ['Foo<Closure(int): void, string>'];
        yield 'shape' => ['Foo<array{a: int}>'];
        yield 'literals' => ['Foo<1, 2.5, true, null, "x">'];
        yield 'constants' => ['Foo<Bar::BAZ, Bar::*>'];
        yield 'this' => ['Foo<$this>'];
        yield 'static' => ['Foo<static>'];

        yield 'array' => ['array<int, string>'];
        yield 'range' => ['int<0, max>'];
        yield 'negative range' => ['int<-10, 10>'];
        yield 'mask of integers' => ['int-mask-of<Foo::*>'];
        yield 'key of' => ['key-of<array{a: int}>'];
        yield 'class string' => ['class-string<Foo|Bar>'];
    }

    #[DataProvider('validArgumentsDataProvider')]
    public function testArgumentsAreAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidArgumentsDataProvider(): iterable
    {
        yield 'empty' => ['Foo<>'];
        yield 'only a comma' => ['Foo<,>'];
        yield 'empty argument' => ['Foo<int,,string>'];
        yield 'leading comma' => ['Foo<,int>'];
        yield 'unclosed' => ['Foo<int'];
        yield 'extra bracket' => ['Foo<int>>'];
        yield 'three names' => ['Foo<A B C>'];
        yield 'two names, the first is no variance' => ['Foo<A B>'];
        yield 'in is no variance' => ['Foo<in T>'];
        yield 'out is no variance' => ['Foo<out T>'];
        yield 'keyword is is no variance' => ['Foo<is T>'];
        yield 'variance in upper case' => ['Foo<COVARIANT T>'];
        yield 'two variances' => ['Foo<covariant contravariant T>'];
        yield 'question mark' => ['Foo<?>'];
        yield 'colon' => ['Foo<T: int>'];
        yield 'assignment' => ['Foo<T = int>'];
        yield 'bound' => ['Foo<T of int>'];
        yield 'plus' => ['Foo<+T>'];
        yield 'minus' => ['Foo<-T>'];
    }

    #[DataProvider('invalidArgumentsDataProvider')]
    public function testArgumentsAreRejected(string $source): void
    {
        self::assertRejected($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validParameterDataProvider(): iterable
    {
        yield 'name' => ['T'];
        yield 'upper bound by of' => ['T of U'];
        yield 'upper bound by as' => ['T as U'];
        yield 'lower bound' => ['T super U'];
        yield 'both bounds' => ['T of A super B'];
        yield 'union as a bound' => ['T of A|B'];
        yield 'generic as a bound' => ['T of Foo<int>'];
        yield 'default' => ['T = X'];
        yield 'bound and default' => ['T of U = X'];
        yield 'union as a default' => ['T = int|string'];
        yield 'literal as a default' => ['T of int = 42'];
        yield 'covariant' => ['covariant T'];
        yield 'variance, bounds and default' => ['covariant T of U super V = X'];
        yield 'keyword is as a name' => ['is'];
        yield 'keyword of as a name' => ['of'];
        yield 'keyword of as a name with a bound' => ['of of int'];
        yield 'keyword as as a bound of a keyword' => ['of as int'];
        yield 'builtin as a name' => ['int'];
        yield 'literal word as a name' => ['null'];
    }

    #[DataProvider('validParameterDataProvider')]
    public function testTemplateParameterIsAccepted(string $source): void
    {
        self::assertAccepted($source, SyntaxChecker::TemplateParameter);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidParameterDataProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'bound without a type' => ['T of'];
        yield 'default without a type' => ['T ='];
        yield 'lower bound without a type' => ['T super'];
        yield 'two upper bounds' => ['T of U of V'];
        yield 'two lower bounds' => ['T super U super V'];
        yield 'lower bound before an upper one' => ['T super U of V'];
        yield 'colon' => ['T: int'];
        yield 'extends' => ['T extends int'];
        yield 'upper case bound' => ['T OF int'];
        yield 'upper case lower bound' => ['T SUPER int'];
        yield 'two parameters' => ['T, U'];
        yield 'a type' => ['T[]'];
    }

    #[DataProvider('invalidParameterDataProvider')]
    public function testTemplateParameterIsRejected(string $source): void
    {
        self::assertRejected($source, SyntaxChecker::TemplateParameter);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validParameterListDataProvider(): iterable
    {
        yield 'one parameter' => ['callable<T>(T): T'];
        yield 'two parameters' => ['callable<T, U>(T): U'];
        yield 'without spaces' => ['callable<T,U>(T): U'];
        yield 'trailing comma' => ['callable<T, U,>(T): U'];
        yield 'bound' => ['callable<T of int>(T): T'];
        yield 'variance, bound and default' => ['callable<covariant T of A super B = C>(T): T'];
        yield 'closure' => ['Closure<T>(T): T'];
        yield 'fully qualified closure' => ['\\Closure<T>(T): T'];
        yield 'pure callable' => ['pure-callable<T>(T): T'];
        yield 'any name' => ['Foo<T>(T): T'];
        yield 'no parameters' => ['callable<T>(): void'];
        yield 'keyword as a name' => ['callable<of>(of): of'];
        yield 'builtin as a name' => ['callable<int>(int): int'];
        yield 'bound by a keyword name' => ['callable<of of int>(of): of'];
    }

    #[DataProvider('validParameterListDataProvider')]
    public function testTemplateParametersOfCallableAreAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidParameterListDataProvider(): iterable
    {
        yield 'empty' => ['callable<>(): void'];
        yield 'bound without a type' => ['callable<T of>(): void'];
        yield 'default without a type' => ['callable<T = >(): void'];
        yield 'colon' => ['callable<T: int>(T): T'];
        yield 'without parentheses' => ['callable<T of U>'];
    }

    #[DataProvider('invalidParameterListDataProvider')]
    public function testTemplateParametersOfCallableAreRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
