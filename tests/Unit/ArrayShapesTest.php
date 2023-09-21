<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class ArrayShapesTest extends TestCase
{
    public function testFields(): void
    {
        $this->assertStatementSame('array{a,b,c}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(a)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(b)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testEmptyShape(): void
    {
        $this->assertStatementSame('array{}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
            OUTPUT);
    }

    public function testUnsealedFields(): void
    {
        $this->assertStatementSame('array{a,b,c,...}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(a)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(b)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneAnonymousArgument(): void
    {
        $this->assertStatementSame('array{int}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyAnonymousFields(): void
    {
        $this->assertStatementSame('array{int, string}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(int)
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedAnonymousFields(): void
    {
        $this->assertStatementSame('array{Some\Any{int, string}}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(Some\Any)
                    Stmt\Shape\FieldsListNode
                      Stmt\Shape\FieldNode
                        Stmt\NamedTypeNode
                          Name(int)
                      Stmt\Shape\FieldNode
                        Stmt\NamedTypeNode
                          Name(string)
            OUTPUT);
    }

    public function testNamedArgument(): void
    {
        $this->assertStatementSame('array{name:int}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\NamedFieldNode(name)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(int)
                  Literal\StringLiteralNode(name)
            OUTPUT);
    }

    public function testMixedFields(): void
    {
        $this->assertStatementSame('array{some, required:a, optional?:b}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
              Stmt\Shape\FieldsListNode
                Stmt\Shape\FieldNode
                  Stmt\NamedTypeNode
                    Name(some)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\Shape\FieldNode
                    Stmt\NamedTypeNode
                      Name(a)
                  Literal\StringLiteralNode(required)
                Stmt\Shape\OptionalFieldNode
                  Stmt\Shape\NamedFieldNode(optional)
                    Stmt\Shape\FieldNode
                      Stmt\NamedTypeNode
                        Name(b)
                    Literal\StringLiteralNode(optional)
            OUTPUT);
    }
}
