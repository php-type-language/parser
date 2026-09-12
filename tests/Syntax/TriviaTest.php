<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the whitespace and the comments a type is written among, which
 * part the tokens and reach no tree of their own.
 */
#[Group('unit'), Group('type-lang/parser')]
final class TriviaTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function commentedUnionDataProvider(): iterable
    {
        yield 'block comment' => ['int /* c */ | string'];
        yield 'two block comments' => ['int /* a */ /* b */ | string'];
        yield 'line comment' => ["int // c\n| string"];
        yield 'hash comment' => ["int # c\n| string"];
        yield 'empty hash comment' => ["int #\n| string"];
        yield 'comment behind the delimiter' => ['int|/* c */string'];
        yield 'comment on either side' => ['/* a */int|string/* b */'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('commentedUnionDataProvider')]
    public function testACommentPartsTwoTokens(string $type): void
    {
        self::assertSame(<<<'AST'
            UnionTypeNode
              NamedTypeNode
                Name(int)
              NamedTypeNode
                Name(string)
            AST, $this->parseAndPrint($type));
    }

    /**
     * A comment ends the line it is written on, so whatever follows the line
     * is read as usual.
     */
    public function testALineCommentEndsWithItsLine(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  NamedTypeNode
                    Name(int)
            AST, $this->parseAndPrint("array{\n  // the key\n  a: int,\n}"));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function commentedNameDataProvider(): iterable
    {
        yield 'in front of the separator' => ['Some/* c */\Any'];
        yield 'behind the separator' => ['Some\/* c */Any'];
    }

    /**
     * A separator parts the segments of a name on its own, so a comment
     * standing beside it parts nothing.
     *
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('commentedNameDataProvider')]
    public function testACommentStandsInsideAName(string $type): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Some\Any)
            AST, $this->parseAndPrint($type));
    }

    public function testACommentStandsInsideAnEmptyShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
            AST, $this->parseAndPrint('array{ /* c */ }'));
    }

    public function testACommentStandsBetweenANameAndItsParameters(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable/* c */(): void'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function spacedTypeDataProvider(): iterable
    {
        yield 'around the arguments' => ['Some < int , string >'];
        yield 'no space at all' => ['Some<int,string>'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('spacedTypeDataProvider')]
    public function testSpaceAroundATokenIsOfNoMeaning(string $type): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Some)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(int)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(string)
            AST, $this->parseAndPrint($type));
    }

    public function testSpaceStandsAroundEveryPartOfACallable(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(int)
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable ( int ) : void'));
    }

    public function testATypeIsSurroundedByBlankLines(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(int)
            AST, $this->parseAndPrint("\n\n  int  \n\n"));
    }

    /**
     * The two words of an "is not" are two tokens, so whatever stands between
     * any other pair of tokens stands between them as well.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function partedIsNotDataProvider(): iterable
    {
        yield 'a single space' => ['A is not B ? C : D'];
        yield 'several spaces' => ['A is  not B ? C : D'];
        yield 'a tabulation' => ["A is\tnot B ? C : D"];
        yield 'a line terminator' => ["A is\nnot B ? C : D"];
        yield 'a block comment' => ['A is/* c */not B ? C : D'];
        yield 'a line comment' => ["A is // c\nnot B ? C : D"];
        yield 'a comment and a line terminator' => ["A is /* c */\n not B ? C : D"];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('partedIsNotDataProvider')]
    public function testTriviaPartsTheWordsOfAnIsNot(string $type): void
    {
        self::assertSame(<<<'AST'
            TernaryExpressionNode
              Condition\NotEqualConditionNode
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              NamedTypeNode
                Name(D)
            AST, $this->parseAndPrint($type));
    }

    /**
     * The words are read the way they are written, so a single word that is
     * spelled like the two of them is an ordinary name.
     */
    public function testTheWordsOfAnIsNotAreTwoWords(): void
    {
        $this->expectParsingException();

        $this->parse('A isnot B ? C : D');
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function casedIsNotDataProvider(): iterable
    {
        yield 'uppercase' => ['A IS NOT B ? C : D'];
        yield 'uppercase negation alone' => ['A is NOT B ? C : D'];
        yield 'mixed case' => ['A Is NoT B ? C : D'];
    }

    /**
     * The words are written the one way they are written, so a word of any
     * other case is an ordinary name and opens no condition.
     *
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('casedIsNotDataProvider')]
    public function testTheWordsOfAnIsNotAreCaseSensitive(string $type): void
    {
        $this->expectParsingException();

        $this->parse($type);
    }

    public function testAnUnterminatedBlockCommentIsRefused(): void
    {
        $this->expectParsingException('unexpected "/*"');

        $this->parse('/* the comment that never ends');
    }

    public function testACommentAloneIsNoType(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('// only a comment');
    }

    public function testSpaceAloneIsNoType(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse("  \n\t ");
    }
}
