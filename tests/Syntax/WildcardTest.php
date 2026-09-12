<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the asterisk written in the place of a template argument that is
 * left unsaid.
 */
#[Group('unit'), Group('type-lang/parser')]
final class WildcardTest extends SyntaxTestCase
{
    public function testWildcardTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Collection)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  WildcardNode(*)
            AST, $this->parseAndPrint('Collection<*>'));
    }

    public function testWildcardBesideAType(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(array-key)
                Template\TemplateArgumentNode
                  WildcardNode(*)
            AST, $this->parseAndPrint('HashMap<array-key, *>'));
    }

    public function testWildcardCarriesAHint(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Collection)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  WildcardNode(*)
                  Identifier(out)
            AST, $this->parseAndPrint('Collection<out *>'));
    }

    public function testWildcardIsReadInsideAnUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  WildcardNode(*)
              Shape\FieldsListNode(isSealed=false)
            AST, $this->parseAndPrint('array{...<*>}'));
    }

    public function testWildcardOffsetIsTheOneItIsWrittenAt(): void
    {
        $node = $this->parse('Collection<int, *>');

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments->items[1];

        self::assertInstanceOf(\TypeLang\Type\WildcardNode::class, $argument->value);
        self::assertSame(16, $argument->value->offset);
    }

    public function testWildcardIsNotAType(): void
    {
        $this->expectParsingException();

        $this->parse('int|*');
    }

    public function testWildcardIsNotACallableParameter(): void
    {
        $this->expectParsingException();

        $this->parse('callable(*): void');
    }

    public function testWildcardIsRefusedWhenGenericsAreDisabled(): void
    {
        $this->expectParsingException('Template arguments not allowed');

        $this->parse('Collection<*>', ['generics' => false]);
    }
}
