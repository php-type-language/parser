<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class GenericsTest extends TestCase
{
    public function testArguments(): void
    {
        $this->assertTypeStatementSame('array<a,b,c>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ArgumentsListNode
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(a)
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(b)
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneArgument(): void
    {
        $this->assertTypeStatementSame('array<int>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ArgumentsListNode
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyArguments(): void
    {
        $this->assertTypeStatementSame('array<int, string>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ArgumentsListNode
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedGeneric(): void
    {
        $this->assertTypeStatementSame('array<Some\Any<int, string>>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ArgumentsListNode
                Stmt\Template\ArgumentNode
                  Stmt\NamedTypeNode
                    Name(Some\Any)
                    Stmt\Template\ArgumentsListNode
                      Stmt\Template\ArgumentNode
                        Stmt\NamedTypeNode
                          Name(int)
                      Stmt\Template\ArgumentNode
                        Stmt\NamedTypeNode
                          Name(string)
            OUTPUT);
    }
}
