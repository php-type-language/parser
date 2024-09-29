<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Types;

use PHPUnit\Framework\Attributes\Group;

#[Group('unit'), Group('type-lang/parser')]
class GenericTypesTest extends TypesTestCase
{
    public function testArguments(): void
    {
        $this->assertTypeStatementSame('array<a,b,c>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(a)
                      Identifier(a)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(b)
                      Identifier(b)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(c)
                      Identifier(c)
            OUTPUT);
    }

    public function testOneArgument(): void
    {
        $this->assertTypeStatementSame('array<int>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
            OUTPUT);
    }

    public function testManyArguments(): void
    {
        $this->assertTypeStatementSame('array<int, string>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(string)
                      Identifier(string)
            OUTPUT);
    }

    public function testNestedGeneric(): void
    {
        $this->assertTypeStatementSame('array<Some\Any<int, string>>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(Some\Any)
                      Identifier(Some)
                      Identifier(Any)
                    Stmt\Template\TemplateArgumentsListNode
                      Stmt\Template\TemplateArgumentNode
                        Stmt\NamedTypeNode
                          Name(int)
                            Identifier(int)
                      Stmt\Template\TemplateArgumentNode
                        Stmt\NamedTypeNode
                          Name(string)
                            Identifier(string)
            OUTPUT);
    }

    public function testHintedGenericParam(): void
    {
        $this->assertTypeStatementSame('array<out T, in U>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Identifier(out)
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                Stmt\Template\TemplateArgumentNode
                  Identifier(in)
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
            OUTPUT);
    }
}
