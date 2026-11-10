<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Conditional types (grammar/conditions.pp3).
 */
final class ConditionsTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function validConditionDataProvider(): iterable
    {
        yield 'condition' => ['T is int ? A : B'];
        yield 'negation' => ['T is not int ? A : B'];
        yield 'grouped' => ['(T is int ? A : B)'];
        yield 'without spaces around the branches' => ['T is int?A:B'];
        yield 'with a space on one side' => ['T is int ?A: B'];
        yield 'with many spaces' => ['T is  int  ?  A  :  B'];
        yield 'on several lines' => ["T\nis\nint\n?\nA\n:\nB"];

        yield 'union as a target' => ['T is int|string ? A : B'];
        yield 'nullable as a target' => ['T is ?int ? A : B'];
        yield 'list as a target' => ['T is int[] ? A : B'];
        yield 'generic as a target' => ['T is Foo<int> ? A : B'];
        yield 'callable as a target' => ['T is callable(): int ? A : B'];
        yield 'shape as a target' => ['T is array{a: int} ? A : B'];
        yield 'literal as a target' => ["T is 'a' ? A : B"];
        yield 'integer as a target' => ['T is 1 ? A : B'];
        yield 'null as a target' => ['T is null ? A : B'];
        yield 'constant as a target' => ['T is Foo::BAR ? A : B'];
        yield 'mask as a target' => ['T is Foo::* ? A : B'];
        yield 'static as a target' => ['T is static ? A : B'];
        yield 'this as a target' => ['T is $this ? A : B'];

        yield 'variable as a subject' => ['$a is int ? A : B'];
        yield 'variable as a subject with a negation' => ['$a is not int ? A : B'];
        yield 'variable as a target' => ['A is $b ? C : D'];
        yield 'variables on both sides' => ['$a is $b ? C : D'];
        yield 'this as a subject' => ['$this is B ? C : D'];
        yield 'grouped variable' => ['($a is int ? A : B)'];
        yield 'condition on a parameter' => ['callable(int $a): ($a is int ? A : B)'];

        yield 'union in a branch' => ['T is int ? A|B : C'];
        yield 'union in the last branch' => ['T is int ? A : B|C'];
        yield 'group in a branch' => ['T is int ? (A|B) : C'];
        yield 'list in a branch' => ['T is int ? A[] : B[]'];
        yield 'callable in a branch' => ['T is int ? callable(): int : callable(): string'];
        yield 'shape in a branch' => ['T is int ? array{a: int} : array{b: string}'];
        yield 'constants in branches' => ['T is int ? Foo::A : Foo::B'];
        yield 'constant glued to the colon' => ['T is int ? Foo::BAR:B'];
        yield 'nullable branch' => ['T is int ?? A : B'];

        yield 'condition in the last branch' => ['T is int ? A : B is C ? D : E'];
        yield 'condition in the first branch' => ['T is int ? A is C ? D : E : B'];
        yield 'grouped condition in the first branch' => ['T is int ? (A is C ? D : E) : B'];
        yield 'grouped condition in the last branch' => ['T is int ? A : (B is C ? D : E)'];

        yield 'condition is the weakest: union subject' => ['C|T is int ? A : B'];
        yield 'condition is the weakest: nullable subject' => ['?T is int ? A : B'];
        yield 'condition is the weakest: union subject of a nested condition' => ['T is int ? A : B|C is D ? E : F'];
        yield 'grouped condition in a union' => ['(T is int ? A : B)|C'];
        yield 'grouped condition on the right of a union' => ['C|(T is int ? A : B)'];
        yield 'grouped condition in a list' => ['(T is int ? A : B)[]'];
        yield 'condition in an argument' => ['Foo<T is int ? A : B, C>'];
        yield 'condition in a field' => ['array{a: T is int ? A : B}'];
        yield 'condition in a parameter' => ['callable(T is int ? A : B $x): void'];

        yield 'negation of a nullable' => ['T is not ?A ? X : Y'];
        yield 'negation of a nullable union' => ['T is not ?A|B ? X : Y'];
        yield 'negation of a grouped nullable' => ['T is not (?A) ? X : Y'];
        yield 'negation of a nullable list' => ['T is not (?A)[] ? X : Y'];
        yield 'negation with a comment between the words' => ['T is /* c */ not int ? A : B'];
        yield 'negation with a line break between the words' => ["T is\nnot int ? A : B"];
        yield 'target that opens with the word not' => ['T is notX ? A : B'];
        yield 'keywords as names' => ['is is is ? is : is'];
        yield 'keyword not as a subject' => ['not is int ? A : B'];
    }

    #[DataProvider('validConditionDataProvider')]
    public function testConditionIsAccepted(string $source): void
    {
        self::assertAccepted($source);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function invalidConditionDataProvider(): iterable
    {
        yield 'no branches' => ['T is int ?'];
        yield 'no else' => ['T is int ? A'];
        yield 'no else behind a colon' => ['T is int ? A :'];
        yield 'no then' => ['T is int ? : B'];
        yield 'no question mark' => ['T is int A : B'];
        yield 'no target' => ['T is ? A : B'];
        yield 'no operator' => ['T ? A : B'];
        yield 'no operator on a variable' => ['$a ? A : B'];
        yield 'negation twice' => ['T is not not int ? A : B'];
        yield 'keyword not as a target' => ['T is not ? A : B'];
        yield 'keyword not as a target and a subject' => ['not is not ? A : B'];
        yield 'keyword not as a target of a union' => ['T is not|int ? A : B'];
        yield 'elvis' => ['T is int ?: B'];
        yield 'two branches in a row' => ['T is int ? A, B : C'];
        yield 'trailing semicolon' => ['T is int ? A : B ;'];
        yield 'trailing comma' => ['T is int ? A : B,'];
        yield 'two conditions in a row' => ['T is int ? A : B ? C : D'];
        yield 'variable alone' => ['$a'];
        yield 'variable as a target alone' => ['T is $b'];

        yield 'bound instead of a condition' => ['T of int ? A : B'];
        yield 'alias instead of a condition' => ['T as int ? A : B'];
        yield 'equality' => ['T == int ? A : B'];
        yield 'strict equality' => ['T === int ? A : B'];
        yield 'inequality' => ['T != int ? A : B'];
        yield 'strict inequality' => ['T !== int ? A : B'];
        yield 'instanceof' => ['T instanceof int ? A : B'];
        yield 'extends' => ['T extends int ? A : B'];
        yield 'upper case keywords' => ['T IS NOT int ? A : B'];
        yield 'upper case is' => ['T IS int ? A : B'];
    }

    #[DataProvider('invalidConditionDataProvider')]
    public function testConditionIsRejected(string $source): void
    {
        self::assertRejected($source);
    }
}
