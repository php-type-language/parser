<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the asterisk written in the place of a template argument that is
 * left unsaid.
 */
#[Group('unit'), Group('type-lang/parser')]
final class WildcardTest extends AstTestCase
{
    protected function setUp(): void
    {
        // Enabled for step 12
    }

    public function testWildcardTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Collection)
              Template\TemplateListNode
                Template\WildcardTemplateArgumentNode
            AST, $this->parseAndPrint('Collection<*>'));
    }

    public function testWildcardBesideAType(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateListNode
                Template\TypedTemplateArgumentNode
                  NamedTypeNode
                    Name(array-key)
                Template\WildcardTemplateArgumentNode
            AST, $this->parseAndPrint('HashMap<array-key, *>'));
    }

    public function testWildcardIsReadInsideAnUnsealedShape(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Template\TemplateListNode
                Template\WildcardTemplateArgumentNode
              Shape\FieldListNode(isSealed=false)
            AST, $this->parseAndPrint('array{...<*>}'));
    }

    public function testWildcardOffsetIsTheOneItIsWrittenAt(): void
    {
        $node = $this->parse('Collection<int, *>');

        self::assertInstanceOf(\TypeLang\Type\NamedTypeNode::class, $node);
        self::assertNotNull($node->arguments);

        $argument = $node->arguments->items[1];

        self::assertInstanceOf(\TypeLang\Type\Template\WildcardTemplateArgumentNode::class, $argument);
        self::assertSame(16, $argument->offset);
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
        $this->expectFeatureException('Generics feature is not allowed');

        $this->parse('Collection<*>', ['generics' => false]);
    }
}
