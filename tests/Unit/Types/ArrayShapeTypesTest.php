<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Types;

use PHPUnit\Framework\Attributes\Group;

#[Group('unit'), Group('type-lang/parser')]
class ArrayShapeTypesTest extends TypesTestCase
{
    public function testFields(): void
    {
        $this->assertTypeStatementSame('array{a,b,c}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(a)
                      Identifier(a)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(b)
                      Identifier(b)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(c)
                      Identifier(c)
            OUTPUT);
    }

    public function testEmptyShape(): void
    {
        $this->assertTypeStatementSame('array{}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
            OUTPUT);
    }

    public function testUnsealedFields(): void
    {
        $this->assertTypeStatementSame('array{a,b,c,...}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(unsealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(a)
                      Identifier(a)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(b)
                      Identifier(b)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(c)
                      Identifier(c)
            OUTPUT);
    }

    public function testOneAnonymousArgument(): void
    {
        $this->assertTypeStatementSame('array{int}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
            OUTPUT);
    }

    public function testManyAnonymousFields(): void
    {
        $this->assertTypeStatementSame('array{int, string}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(string)
                      Identifier(string)
            OUTPUT);
    }

    public function testNestedAnonymousFields(): void
    {
        $this->assertTypeStatementSame('array{Some\Any{int, string}}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\ImplicitFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(Some\Any)
                      Identifier(Some)
                      Identifier(Any)
                    Stmt\Shape\FieldsListNode(sealed)
                      Stmt\Shape\ImplicitFieldNode(required)
                        Stmt\NamedTypeNode
                          Name(int)
                            Identifier(int)
                      Stmt\Shape\ImplicitFieldNode(required)
                        Stmt\NamedTypeNode
                          Name(string)
                            Identifier(string)
            OUTPUT);
    }

    public function testNamedArgument(): void
    {
        $this->assertTypeStatementSame('array{name:int}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                  Identifier(name)
            OUTPUT);
    }

    public function testStringNamedArgument(): void
    {
        $this->assertTypeStatementSame('array{"name":int}', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\StringNamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(int)
                      Identifier(int)
                  Literal\StringLiteralNode("name")
            OUTPUT);
    }

    public function testMixedFields(): void
    {
        $this->assertTypeStatementFails(<<<'PHP'
            array{ int, named: int }
            PHP, 'explicit and implicit shape keys');
    }

    public function testAllFields(): void
    {
        $this->assertTypeStatementSame(<<<'PHP'
            array{
                required: a,
                optional?: b,
                "string_required": c,
                "string_optional"?: d
            }
            PHP, <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(a)
                      Identifier(a)
                  Identifier(required)
                Stmt\Shape\NamedFieldNode(optional)
                  Stmt\NamedTypeNode
                    Name(b)
                      Identifier(b)
                  Identifier(optional)
                Stmt\Shape\StringNamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(c)
                      Identifier(c)
                  Literal\StringLiteralNode("string_required")
                Stmt\Shape\StringNamedFieldNode(optional)
                  Stmt\NamedTypeNode
                    Name(d)
                      Identifier(d)
                  Literal\StringLiteralNode("string_optional")
            OUTPUT);
    }
}
