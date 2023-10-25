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
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(a)
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(b)
                Type\Shape\FieldNode(required)
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
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(a)
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(b)
                Type\Shape\FieldNode(required)
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
                Type\Shape\FieldNode(required)
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
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                Type\Shape\FieldNode(required)
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
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(Some\Any)
                    Type\Shape\FieldsListNode(sealed)
                      Type\Shape\FieldNode(required)
                        Type\NamedTypeNode
                          Name(int)
                      Type\Shape\FieldNode(required)
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
                Type\Shape\NamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Identifier(name)
            OUTPUT);
    }

    public function testStringNamedArgument(): void
    {
        $this->assertStatementSame('array{"name":int}', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\StringNamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(int)
                  Literal\StringLiteralNode("name")
            OUTPUT);
    }

    public function testMixedFields(): void
    {
        $this->assertStatementSame(<<<'PHP'
            array{
                some,
                required: a,
                optional?: b,
                "string_required": c,
                "string_optional"?: d
            }
            PHP, <<<'OUTPUT'
            Type\NamedTypeNode
              Name(array)
              Type\Shape\FieldsListNode(sealed)
                Type\Shape\FieldNode(required)
                  Type\NamedTypeNode
                    Name(some)
                Type\Shape\NamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(a)
                  Identifier(required)
                Type\Shape\NamedFieldNode(optional)
                  Type\NamedTypeNode
                    Name(b)
                  Identifier(optional)
                Type\Shape\StringNamedFieldNode(required)
                  Type\NamedTypeNode
                    Name(c)
                  Literal\StringLiteralNode("string_required")
                Type\Shape\StringNamedFieldNode(optional)
                  Type\NamedTypeNode
                    Name(d)
                  Literal\StringLiteralNode("string_optional")
            OUTPUT);
    }
}
