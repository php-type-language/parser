<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for generic (template argument) grammar and call-site hints.
 */
#[Group('unit'), Group('type-lang/parser')]
final class GenericTest extends SyntaxTestCase
{
    public function testTemplateArguments(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Path\To\ExampleClass)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(U)
            AST, $this->parseAndPrint('Path\\To\\ExampleClass<T, U>'));
    }

    public function testSingleTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Collection)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(User)
            AST, $this->parseAndPrint('Collection<User>'));
    }

    public function testNestedTemplateArguments(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(iterable)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(int)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        Literal\IntLiteralNode(0)
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(max)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Collection)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(User)
            AST, $this->parseAndPrint('iterable<int<0, max>, Collection<User>>'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Request)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(User)
            AST, $this->parseAndPrint('HashMap<Request, User,>'));
    }

    public function testCallSiteHint(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(array-key)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Request)
                  Identifier(covariant)
            AST, $this->parseAndPrint('HashMap<array-key, covariant Request>'));
    }

    /**
     * Without a space after the hint-like identifier the whole token is a
     * relative name: {@code Type<out\Some>} is {@code Type} parameterized
     * with the {@code out\Some} type and no hint.
     */
    public function testHintLikeIdentifierWithoutSpaceIsRelativeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(out\Some)
            AST, $this->parseAndPrint('Type<out\\Some>'));
    }

    /**
     * A space after the identifier turns it into a hint: {@code Type<out \Some>}
     * is {@code Type} parameterized with the {@code \Some} type hinted by
     * {@code out}.
     */
    public function testHintFollowedBySpaceAndFullyQualifiedName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(\Some)
                  Identifier(out)
            AST, $this->parseAndPrint('Type<out \\Some>'));
    }

    /**
     * A multi-segment relative name starting with a hint-like identifier
     * stays a single name as long as no space separates the segments.
     */
    public function testHintLikeIdentifierWithoutSpaceIsNestedRelativeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(out\Some\Deep)
            AST, $this->parseAndPrint('Type<out\\Some\\Deep>'));
    }

    /**
     * A hint may also precede a relative (non fully-qualified) name when the
     * two are separated by a space: {@code Type<out Some>}.
     */
    public function testHintFollowedBySpaceAndRelativeName(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Some)
                  Identifier(out)
            AST, $this->parseAndPrint('Type<out Some>'));
    }

    public function testMissingTemplateArgument(): void
    {
        $this->expectParsingException('an argument list must carry at least one argument');

        $this->parse('example<>');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('an argument list must carry at least one argument');

        $this->parse('example<,T>');
    }

    public function testHintAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('an argument list must be closed with a bracket ">"');

        $this->parse('Collection<42 User>');
    }

    /**
     * A second hint reads as the bound of a template parameter, and a
     * parameter list belongs to a callable, so the statement is refused
     * where the parenthesis it would go on with is missing.
     */
    public function testMultipleHintsAreNotAllowed(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('HashMap<array-key, some covariant Request>');
    }
}
