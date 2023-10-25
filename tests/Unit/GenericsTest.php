<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class GenericsTest extends TestCase
{
    public function testParameters(): void
    {
        $this->assertStatementSame('array<a,b,c>', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(a)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(b)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneParameter(): void
    {
        $this->assertStatementSame('array<int>', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyParameters(): void
    {
        $this->assertStatementSame('array<int, string>', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedGeneric(): void
    {
        $this->assertStatementSame('array<Some\Any<int, string>>', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Template\ParametersListNode
                Type\Template\ParameterNode
                  Type\NamedTypeNode
                    Name(Some\Any)
                    Type\Template\ParametersListNode
                      Type\Template\ParameterNode
                        Type\NamedTypeNode
                          Name(int)
                      Type\Template\ParameterNode
                        Type\NamedTypeNode
                          Name(string)
            OUTPUT);
    }
}
