<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class GenericsTest extends TestCase
{
    public function testParameters(): void
    {
        $this->assertStatementSame('array<a,b,c>', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(a)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(b)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneParameter(): void
    {
        $this->assertStatementSame('array<int>', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyParameters(): void
    {
        $this->assertStatementSame('array<int, string>', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedGeneric(): void
    {
        $this->assertStatementSame('array<Some\Any<int, string>>', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Template\ParametersListNode
                Stmt\Type\Template\ParameterNode
                  Stmt\Type\NamedTypeNode
                    Name(Some\Any)
                    Stmt\Type\Template\ParametersListNode
                      Stmt\Type\Template\ParameterNode
                        Stmt\Type\NamedTypeNode
                          Name(int)
                      Stmt\Type\Template\ParameterNode
                        Stmt\Type\NamedTypeNode
                          Name(string)
            OUTPUT);
    }
}
