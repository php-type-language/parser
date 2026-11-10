<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Whitespaces and comments between the tokens of a type leave the tree of it
 * as it is.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TriviaTest extends AstTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function triviaDataProvider(): iterable
    {
        // The type with the trivia, the same type with none
        yield 'blank lines around' => ["\n\n int \n\n", 'int'];
        yield 'block comments around' => ['/* a */int|string/* b */', 'int|string'];
        yield 'block comment beside a bar' => ['int /* c */ | string', 'int|string'];
        yield 'two block comments' => ['int /* a */ /* b */ | string', 'int|string'];
        yield 'line comment' => ["int // c\n| string", 'int|string'];
        yield 'hash comment' => ["int # c\n| string", 'int|string'];
        yield 'empty hash comment' => ["int #\n| string", 'int|string'];
        yield 'comment behind a bar' => ['int|/* c */string', 'int|string'];
        yield 'comment beside the double colon' => ['Foo /* c */ :: /* c */ BAR', 'Foo::BAR'];
        yield 'spaces inside the arguments' => ['Some < int , string >', 'Some<int,string>'];
        yield 'comments inside the arguments' => ['Some</* a */int/* b */,/* c */string>', 'Some<int,string>'];
        yield 'spaces inside the brackets' => ['T /* c */ [ K ]', 'T[K]'];
        yield 'comment inside an empty shape' => ['array{ /* c */ }', 'array{}'];
        yield 'spaces inside a shape' => ['array{ a : int , b ? : string , }', 'array{a:int,b?:string,}'];
        yield 'comments inside a shape' => ['array{/* a */ a: int /* b */, b: string}', 'array{a: int, b: string}'];
        yield 'spaces and comments inside a callable' => [
            "callable( int /* c */ \$a , // x\n string ... \$b ) : void",
            'callable(int $a,string ...$b):void',
        ];
        yield 'comments inside a condition' => ['A is /* c */ B /* d */ ? C /* e */ : D', 'A is B ? C : D'];
        yield 'line breaks inside a negation' => ["A is\nnot\nB ? C : D", 'A is not B ? C : D'];
        yield 'comments inside a negation' => ['A is /* c */ not /* d */ B ? C : D', 'A is not B ? C : D'];
        yield 'comment between a name and the parameters' => ['callable /* c */ (int)', 'callable(int)'];
    }

    /**
     * @param non-empty-string $withTrivia
     * @param non-empty-string $withNone
     * @throws \Throwable
     */
    #[DataProvider('triviaDataProvider')]
    public function testTriviaLeavesTheTreeAsItIs(string $withTrivia, string $withNone): void
    {
        self::assertSame($this->parseAndPrint($withNone), $this->parseAndPrint($withTrivia));
    }
}
