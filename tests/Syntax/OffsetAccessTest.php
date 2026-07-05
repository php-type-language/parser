<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the type offset access syntax (e.g. "T['offset']").
 */
#[Group('unit'), Group('type-lang/parser')]
final class OffsetAccessTest extends SyntaxTestCase
{
    public function testStringOffset(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
              Literal\StringLiteralNode('offset')
              NamedTypeNode
                Name(T)
                  Identifier(T)
            AST, $this->parseAndPrint("T['offset']"));
    }

    public function testDependentKeyOffset(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
              NamedTypeNode
                Name(U)
                  Identifier(U)
              NamedTypeNode
                Name(T)
                  Identifier(T)
            AST, $this->parseAndPrint('T[U]'));
    }

    public function testShapeWithNumericOffset(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
              Literal\IntLiteralNode(0)
              NamedTypeNode
                Name(array)
                  Identifier(array)
                Shape\FieldsListNode(sealed)
                  Shape\ImplicitFieldNode(required)
                    NamedTypeNode
                      Name(int)
                        Identifier(int)
                  Shape\ImplicitFieldNode(required)
                    NamedTypeNode
                      Name(string)
                        Identifier(string)
            AST, $this->parseAndPrint('array{int, string}[0]'));
    }

    public function testComplexOffsetWithGenericsAndShapes(): void
    {
        self::assertSame(<<<'AST'
            TypeOffsetAccessNode
              NamedTypeNode
                Name(object)
                  Identifier(object)
                Shape\FieldsListNode(unsealed)
                  Shape\NamedFieldNode(required)
                    Identifier(key)
                    NamedTypeNode
                      Name(int)
                        Identifier(int)
              NamedTypeNode
                Name(T)
                  Identifier(T)
                Template\TemplateArgumentListNode
                  Template\TemplateArgumentNode
                    NamedTypeNode
                      Name(U)
                        Identifier(U)
            AST, $this->parseAndPrint('T<U>[object{key: int, ...}]'));
    }

    public function testOffsetCannotBeDoubleBracketed(): void
    {
        $this->expectParsingException('unexpected "["');

        $this->parse('Collection[[Some]]');
    }

    public function testTypeMustPrecedeOffset(): void
    {
        $this->expectParsingException('unexpected "{"');

        $this->parse("Collection['key']{key: string}");
    }
}
