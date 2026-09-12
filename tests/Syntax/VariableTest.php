<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\TernaryExpressionNode;
use TypeLang\Type\ThisNode;
use TypeLang\Type\VariableNode;

/**
 * Tests for the "$this" written as a type and for the variables written
 * beside one.
 */
#[Group('unit'), Group('type-lang/parser')]
final class VariableTest extends SyntaxTestCase
{
    public function testThisIsATypeOfItsOwn(): void
    {
        self::assertSame('ThisNode', $this->parseAndPrint('$this'));
    }

    public function testThisStandsAsAReturnType(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
              ThisNode
            AST, $this->parseAndPrint('callable(): $this'));
    }

    public function testThisStandsAsAParameterType(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  ThisNode
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable($this): void'));
    }

    /**
     * The same word names a parameter once a type stands in front of it, so
     * it is read as a variable and not as a type.
     */
    public function testThisNamesAParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(callable)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(int)
                  VariableNode
                    Identifier(this)
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('callable(int $this): void'));
    }

    public function testThisIsATemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Some)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  ThisNode
            AST, $this->parseAndPrint('Some<$this>'));
    }

    public function testThisIsAShapeValue(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(a)
                  ThisNode
            AST, $this->parseAndPrint('array{a: $this}'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function wrappedThisDataProvider(): iterable
    {
        yield 'list' => ['$this[]', 'TypesListNode'];
        yield 'nullable' => ['?$this', 'NullableTypeNode'];
        yield 'union' => ['$this|null', 'UnionTypeNode'];
    }

    /**
     * @param non-empty-string $type
     * @param non-empty-string $wrapper
     * @throws \Throwable
     */
    #[DataProvider('wrappedThisDataProvider')]
    public function testThisIsWrappedLikeAnyOtherType(string $type, string $wrapper): void
    {
        self::assertStringStartsWith($wrapper, $this->parseAndPrint($type));
    }

    /**
     * A condition reads its subject as a type first, so the word is the type
     * and not the variable it is spelled like.
     */
    public function testThisStandsAsATypeOnTheLeftOfACondition(): void
    {
        $node = $this->parse('$this is B ? C : D');

        self::assertInstanceOf(TernaryExpressionNode::class, $node);
        self::assertInstanceOf(ThisNode::class, $node->condition->subject);
    }

    /**
     * The token is written the one way it is written, so a word that only
     * looks like it is an ordinary variable.
     */
    public function testTheThisTokenIsCaseSensitive(): void
    {
        $node = $this->parse('$This is B ? C : D');

        self::assertInstanceOf(TernaryExpressionNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame('This', $node->condition->subject->name->value);
    }

    public function testAVariableStandsBesideACondition(): void
    {
        $node = $this->parse('$value is B ? C : D');

        self::assertInstanceOf(TernaryExpressionNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame('value', $node->condition->subject->name->value);
    }

    /**
     * A variable names a place a value is kept in, so it is no type and
     * stands nowhere a type is expected.
     */
    public function testAVariableIsNoTypeOfItsOwn(): void
    {
        $this->expectParsingException();

        $this->parse('$value');
    }

    public function testAVariableIsNoTemplateArgument(): void
    {
        $this->expectParsingException();

        $this->parse('Some<$value>');
    }

    /**
     * A variable carries no type the rest of the reading could go on with,
     * so it stands nowhere a type is wrapped, joined or indexed. The subject
     * of a condition is the one place a variable is read.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableBesideATypeDataProvider(): iterable
    {
        yield 'a nullable' => ['?$value is B ? C : D'];
        yield 'a union' => ['int|$value is B ? C : D'];
        yield 'an intersection' => ['int&$value is B ? C : D'];
        yield 'a union it opens' => ['$value|int is B ? C : D'];
        yield 'a list' => ['$value[] is B ? C : D'];
        yield 'an offset' => ['$value[0] is B ? C : D'];
        yield 'a group' => ['($value)'];
        yield 'a shape value' => ['array{a: $value}'];
        yield 'a return type' => ['callable(): $value'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('variableBesideATypeDataProvider')]
    public function testAVariableStandsInNoTypePosition(string $type): void
    {
        // The token is reported the way any other unexpected one is, rather
        // than raised as an error of the parser itself.
        $this->expectException(UnexpectedTokenException::class);

        $this->parse($type);
    }

    public function testThisCarriesNoClassConstant(): void
    {
        $this->expectParsingException('unexpected "::"');

        $this->parse('$this::CONST');
    }

    public function testAParameterNameStandsBehindItsTypeAlone(): void
    {
        $this->expectParsingException();

        $this->parse('callable($value int): void');
    }

    /**
     * A dash belongs to a name and not to a variable, the way it does not
     * belong to one in PHP either.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function dashedVariableDataProvider(): iterable
    {
        yield 'an ordinary variable' => ['$value-of is B ? C : D'];
        yield 'the this variable' => ['$this-of is B ? C : D'];
        yield 'a parameter name' => ['callable(int $value-of): void'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('dashedVariableDataProvider')]
    public function testAVariableCarriesNoDash(string $type): void
    {
        $this->expectParsingException();

        $this->parse($type);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableBodyDataProvider(): iterable
    {
        yield 'an underscore' => ['$value_1'];
        yield 'a leading underscore' => ['$_value'];
        yield 'digits' => ['$value42'];
        yield 'a word that opens with this' => ['$thisValue'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('variableBodyDataProvider')]
    public function testAVariableCarriesWhateverANameCarriesBesideADash(string $type): void
    {
        $node = $this->parse($type . ' is B ? C : D');

        self::assertInstanceOf(TernaryExpressionNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame(\substr($type, 1), $node->condition->subject->name->value);
    }
    public function testTheDollarSignAloneIsNoVariable(): void
    {
        $this->expectParsingException();

        $this->parse('$');
    }
}
