<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class ArrayShapesTest extends TestCase
{
    public function testFields(): void
    {
        $this->assertStatementSame('array{a,b,c}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(a)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(b)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testEmptyShape(): void
    {
        $this->assertStatementSame('array{}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
            OUTPUT);
    }

    public function testUnsealedFields(): void
    {
        $this->assertStatementSame('array{a,b,c,...}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(unsealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(a)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(b)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(c)
            OUTPUT);
    }

    public function testOneAnonymousArgument(): void
    {
        $this->assertStatementSame('array{int}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(int)
            OUTPUT);
    }

    public function testManyAnonymousFields(): void
    {
        $this->assertStatementSame('array{int, string}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(int)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(string)
            OUTPUT);
    }

    public function testNestedAnonymousFields(): void
    {
        $this->assertStatementSame('array{Some\Any{int, string}}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(Some\Any)
                    Type\Shape\FieldsListNode(sealed)
                      Type\Shape\FieldNode
                        Type\NamedTypeNode
                          Name(int)
                      Type\Shape\FieldNode
                        Type\NamedTypeNode
                          Name(string)
            OUTPUT);
    }

    public function testNamedArgument(): void
    {
        $this->assertStatementSame('array{name:int}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\NamedFieldNode(name)
                  Type\Shape\FieldNode
                    Type\NamedTypeNode
                      Name(int)
                  Literal\StringLiteralNode(name)
            OUTPUT);
    }

    public function testMixedFields(): void
    {
        $this->assertStatementSame('array{some, required:a, optional?:b}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode
                  Type\NamedTypeNode
                    Name(some)
                Type\Shape\NamedFieldNode(required)
                  Type\Shape\FieldNode
                    Type\NamedTypeNode
                      Name(a)
                  Literal\StringLiteralNode(required)
                Type\Shape\OptionalFieldNode
                  Type\Shape\NamedFieldNode(optional)
                    Type\Shape\FieldNode
                      Type\NamedTypeNode
                        Name(b)
                    Literal\StringLiteralNode(optional)
            OUTPUT);
    }
}
