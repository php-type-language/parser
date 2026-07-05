<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for callable types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class CallableTest extends SyntaxTestCase
{
    public function testCallableWithoutParametersAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
            AST, $this->parseAndPrint('foo()'));
    }

    public function testCallableWithParameterAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
              NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('foo(T): void'));
    }

    public function testComplexNestedCallable(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(a)
                Identifier(a)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(int)
                      Identifier(int)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        Literal\IntLiteralNode(0)
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(max)
                            Identifier(max)
                Callable\CallableParameterNode(simple)
                  CallableTypeNode
                    Name(c)
                      Identifier(c)
                    Callable\CallableParameterListNode
                      Callable\CallableParameterNode(simple)
                        NullableTypeNode
                          NamedTypeNode
                            Name(C)
                              Identifier(C)
                    NamedTypeNode
                      Name(mixed)
                        Identifier(mixed)
              NamedTypeNode
                Name(void)
                  Identifier(void)
            AST, $this->parseAndPrint('a(int<0, max>, c(?C): mixed): void'));
    }

    public function testNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T $name)'));
    }

    public function testMixedNamedAndAnonymousParameters(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(A)
                      Identifier(A)
                  Literal\VariableLiteralNode($a)
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(B)
                      Identifier(B)
                Callable\CallableParameterNode(simple)
                  NamedTypeNode
                    Name(C)
                      Identifier(C)
            AST, $this->parseAndPrint('foo(A $a, B, C)'));
    }

    public function testOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(output)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T&)'));
    }

    public function testOutputNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(output)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(T &$name)'));
    }

    public function testOptionalParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(optional)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T=)'));
    }

    public function testVariadicParameterPrefixSyntax(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(variadic)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(...T)'));
    }

    public function testVariadicParameterPostfixSyntax(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(variadic)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
            AST, $this->parseAndPrint('foo(T...)'));
    }

    public function testVariadicNamedOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
                Identifier(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(output, variadic)
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Literal\VariableLiteralNode($name)
            AST, $this->parseAndPrint('foo(...T &$name)'));
    }

    public function testParameterWithoutTypeIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected "$name"');

        $this->parse('foo(T= $name)');
    }

    public function testAmpersandMustFollowParameterType(): void
    {
        $this->expectParsingException('unexpected "T"');

        $this->parse('foo(&T)');
    }

    public function testVariadicCannotBeBothPrefixAndPostfix(): void
    {
        $this->expectParsingException('Either prefix or postfix variadic syntax should be used, but not both');

        $this->parse('foo(...T...)');
    }

    public function testVariadicParameterCannotHaveDefault(): void
    {
        $this->expectParsingException('Cannot have variadic param with a default');

        $this->parse('foo(T ...$name=)');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected ","');

        $this->parse('foo(,T)');
    }
}
