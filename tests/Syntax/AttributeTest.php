<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the attribute grammar applied to template arguments and shape
 * fields (e.g. "#[name("key")]").
 *
 * Note: the AST dump renders the attribute structure (groups and names) but
 * not the attribute argument values.
 */
#[Group('unit'), Group('type-lang/parser')]
final class AttributeTest extends SyntaxTestCase
{
    public function testSingleAttributeOnTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
            AST, $this->parseAndPrint('HashMap<#[name("key")] T>'));
    }

    public function testMultipleAttributesInOneGroup(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
                      Attribute\AttributeNode
                        Name(out)
                          Identifier(out)
            AST, $this->parseAndPrint('HashMap<#[name("key"), out] T>'));
    }

    public function testMultipleAttributeGroups(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(out)
                          Identifier(out)
            AST, $this->parseAndPrint('HashMap<#[name("key")] #[out] T>'));
    }

    public function testSingleAttributeOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(App\Domain\User)
                Identifier(App)
                Identifier(Domain)
                Identifier(User)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(required)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
                      Identifier(non-empty-string)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
            AST, $this->parseAndPrint('App\\Domain\\User{#[name("user_name")] userName: non-empty-string}'));
    }

    public function testMultipleAttributeGroupsOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
                Identifier(array)
              Shape\FieldsListNode(sealed)
                Shape\NamedFieldNode(optional)
                  Identifier(test)
                  NamedTypeNode
                    Name(App\Domain\User)
                      Identifier(App)
                      Identifier(Domain)
                      Identifier(User)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(serialize)
                          Identifier(serialize)
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(deserialize)
                          Identifier(deserialize)
            AST, $this->parseAndPrint('array{#[serialize("onSerialize")] #[deserialize("onDeserialize")] test?: App\\Domain\\User}'));
    }

    public function testTemplateArgumentAttributeAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('Collection<#[42] User>');
    }

    public function testShapeFieldAttributeAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('Collection{#[42] test?: User}');
    }
}
