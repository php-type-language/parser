<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class ArrayShapesTest extends TestCase
{
    public function testFields(): void
    {
        $this->assertStatementSame('array{a,b,c}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(a)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(b)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testEmptyShape(): void
    {
        $this->assertStatementSame('array{}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
            OUTPUT);
    }

    public function testUnsealedFields(): void
    {
        $this->assertStatementSame('array{a,b,c,...}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(unsealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(a)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(b)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneAnonymousArgument(): void
    {
        $this->assertStatementSame('array{int}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyAnonymousFields(): void
    {
        $this->assertStatementSame('array{int, string}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(int)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedAnonymousFields(): void
    {
        $this->assertStatementSame('array{Some\Any{int, string}}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(Some\Any)
                    Stmt\Type\Shape\FieldsListNode(sealed)
                      Stmt\Type\Shape\FieldNode
                        Stmt\Type\NamedTypeNode
                          Name(int)
                      Stmt\Type\Shape\FieldNode
                        Stmt\Type\NamedTypeNode
                          Name(string)
            OUTPUT);
    }

    public function testNamedArgument(): void
    {
        $this->assertStatementSame('array{name:int}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\NamedFieldNode(name)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(int)
                  Stmt\Literal\StringLiteralNode(name)
            OUTPUT);
    }

    public function testMixedFields(): void
    {
        $this->assertStatementSame('array{some, required:a, optional?:b}', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(array)
              Stmt\Type\Shape\FieldsListNode(sealed)
                Stmt\Type\Shape\FieldNode
                  Stmt\Type\NamedTypeNode
                    Name(some)
                Stmt\Type\Shape\NamedFieldNode(required)
                  Stmt\Type\Shape\FieldNode
                    Stmt\Type\NamedTypeNode
                      Name(a)
                  Stmt\Literal\StringLiteralNode(required)
                Stmt\Type\Shape\OptionalFieldNode
                  Stmt\Type\Shape\NamedFieldNode(optional)
                    Stmt\Type\Shape\FieldNode
                      Stmt\Type\NamedTypeNode
                        Name(b)
                    Stmt\Literal\StringLiteralNode(optional)
            OUTPUT);
    }
}
