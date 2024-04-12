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

    public function testHintedGenericParam(): void
    {
        $this->assertTypeStatementSame('array<out T, in U>', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Template\ArgumentsListNode
                Stmt\Template\ArgumentNode
                  Identifier(out)
                  Stmt\NamedTypeNode
                    Name(T)
                Stmt\Template\ArgumentNode
                  Identifier(in)
                  Stmt\NamedTypeNode
                    Name(U)
            OUTPUT);
    }
}
