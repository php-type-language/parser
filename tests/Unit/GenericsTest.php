<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class GenericsTest extends TestCase
{
    public function testParameters(): void
    {
        $this->assertStatementSame('array<a,b,c>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(a)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(b)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneParameter(): void
    {
        $this->assertStatementSame('array<int>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyParameters(): void
    {
        $this->assertStatementSame('array<int, string>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedGeneric(): void
    {
        $this->assertStatementSame('array<Some\Any<int, string>>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ParametersListNode
                Stmt\Template\ParameterNode
                  Stmt\NamedTypeNode
                    Name(Some\Any)
                    Stmt\Template\ParametersListNode
                      Stmt\Template\ParameterNode
                        Stmt\NamedTypeNode
                          Name(int)
                      Stmt\Template\ParameterNode
                        Stmt\NamedTypeNode
                          Name(string)
            OUTPUT);
    }
}
