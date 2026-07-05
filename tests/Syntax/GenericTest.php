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
                Identifier(Path)
                Identifier(To)
                Identifier(ExampleClass)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                      Identifier(T)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(U)
                      Identifier(U)
            AST, $this->parseAndPrint('Path\\To\\ExampleClass<T, U>'));
    }

    public function testSingleTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(Collection)
                Identifier(Collection)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('Collection<User>'));
    }

    public function testNestedTemplateArguments(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(iterable)
                Identifier(iterable)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(int)
                      Identifier(int)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        Literal\IntLiteralNode(0)
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(max)
                            Identifier(max)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Collection)
                      Identifier(Collection)
                    Template\TemplateArgumentListNode
                      Template\TemplateArgumentNode
                        NamedTypeNode
                          Name(User)
                            Identifier(User)
            AST, $this->parseAndPrint('iterable<int<0, max>, Collection<User>>'));
    }

    public function testTrailingCommaIsAllowed(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(Request)
                      Identifier(Request)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(User)
                      Identifier(User)
            AST, $this->parseAndPrint('HashMap<Request, User,>'));
    }

    public function testCallSiteHint(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(array-key)
                      Identifier(array-key)
                Template\TemplateArgumentNode
                  Identifier(covariant)
                  NamedTypeNode
                    Name(Request)
                      Identifier(Request)
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
                Identifier(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(out\Some)
                      Identifier(out)
                      Identifier(Some)
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
                Identifier(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  Identifier(out)
                  NamedTypeNode
                    Name(\Some)
                      Identifier(Some)
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
                Identifier(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(out\Some\Deep)
                      Identifier(out)
                      Identifier(Some)
                      Identifier(Deep)
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
                Identifier(Type)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  Identifier(out)
                  NamedTypeNode
                    Name(Some)
                      Identifier(Some)
            AST, $this->parseAndPrint('Type<out Some>'));
    }

    public function testMissingTemplateArgument(): void
    {
        $this->expectParsingException('unexpected ">"');

        $this->parse('example<>');
    }

    public function testLeadingCommaIsNotAllowed(): void
    {
        $this->expectParsingException('unexpected ","');

        $this->parse('example<,T>');
    }

    public function testHintAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "User"');

        $this->parse('Collection<42 User>');
    }

    public function testMultipleHintsAreNotAllowed(): void
    {
        $this->expectParsingException('unexpected "Request"');

        $this->parse('HashMap<array-key, some covariant Request>');
    }
}
