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
              Callable\CallableParameterListNode
            AST, $this->parseAndPrint('foo()'));
    }

    public function testCallableWithParameterAndReturnType(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(T)
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('foo(T): void'));
    }

    public function testComplexNestedCallable(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(a)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(int)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        Literal\IntLiteralNode(0)
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(max)
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  CallableTypeNode
                    Name(c)
                    Callable\CallableParameterListNode
                      Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                        NullableTypeNode
                          NamedTypeNode
                            Name(C)
                    NamedTypeNode
                      Name(mixed)
              NamedTypeNode
                Name(void)
            AST, $this->parseAndPrint('a(int<0, max>, c(?C): mixed): void'));
    }

    public function testNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(T)
                  VariableNode
                    Identifier(name)
            AST, $this->parseAndPrint('foo(T $name)'));
    }

    public function testMixedNamedAndAnonymousParameters(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(A)
                  VariableNode
                    Identifier(a)
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(B)
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(C)
            AST, $this->parseAndPrint('foo(A $a, B, C)'));
    }

    public function testOutputParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=true, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(T)
            AST, $this->parseAndPrint('foo(T&)'));
    }

    public function testOutputNamedParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=true, isVariadic=false, isOptional=false)
                  NamedTypeNode
                    Name(T)
                  VariableNode
                    Identifier(name)
            AST, $this->parseAndPrint('foo(T &$name)'));
    }

    public function testOptionalParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=false, isOptional=true)
                  NamedTypeNode
                    Name(T)
            AST, $this->parseAndPrint('foo(T=)'));
    }

    public function testVariadicMarkerCannotPrecedeTheParameterType(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(...T)');
    }

    public function testVariadicParameter(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=false, isVariadic=true, isOptional=false)
                  NamedTypeNode
                    Name(T)
            AST, $this->parseAndPrint('foo(T...)'));
    }

    public function testNameCannotFollowTheDefaultMarker(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(T= $name)');
    }

    public function testAmpersandMustFollowParameterType(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(&T)');
    }

    public function testParameterWithoutATypeIsNotAllowed(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo($name)');
    }

    public function testVariadicParameterCannotHaveDefault(): void
    {
        $this->expectParsingException('Cannot have variadic param with a default');

        $this->parse('foo(T ...$name=)');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(,T)');
    }

    /**
     * A reference marker precedes the variadic one, the way PHP itself writes
     * it: {@code &...$name} rather than {@code ...&$name}.
     */
    public function testReferenceAndVariadicMarkersOfATypedParameterAreOrdered(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=true, isVariadic=true, isOptional=false)
                  NamedTypeNode
                    Name(T)
                  VariableNode
                    Identifier(name)
            AST, $this->parseAndPrint('foo(T &...$name)'));
    }

    public function testTypedParameterCannotPutTheReferenceAfterTheVariadic(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(T ...&$name)');
    }

    public function testModifiersWithoutATypeAreNotAllowed(): void
    {
        $this->expectParsingException('a parameter list must be closed with a bracket ")"');

        $this->parse('foo(&...$name)');
    }

    public function testTypedParameterAllowsBothMarkersWithoutAName(): void
    {
        self::assertSame(<<<'AST'
            CallableTypeNode
              Name(foo)
              Callable\CallableParameterListNode
                Callable\CallableParameterNode(isOutput=true, isVariadic=true, isOptional=false)
                  NamedTypeNode
                    Name(T)
            AST, $this->parseAndPrint('foo(T &...)'));
    }
}
