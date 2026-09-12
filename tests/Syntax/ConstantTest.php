<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\ConstMaskNode;

/**
 * Tests for the constant grammar: global constants, class constants and masks.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ConstantTest extends SyntaxTestCase
{
    public function testGlobalConstantIsInterpretedAsNamedType(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(JSON_THROW_ON_ERROR)
            AST, $this->parseAndPrint('JSON_THROW_ON_ERROR'));
    }

    public function testNamespacedConstantIsInterpretedAsNamedType(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(pcov\version)
            AST, $this->parseAndPrint('pcov\\version'));
    }

    public function testClassConstant(): void
    {
        self::assertSame(<<<'AST'
            ClassConstNode
              Name(ClassName)
              Identifier(CONSTANT_NAME)
            AST, $this->parseAndPrint('ClassName::CONSTANT_NAME'));
    }

    public function testNamespacedClassConstant(): void
    {
        self::assertSame(<<<'AST'
            ClassConstNode
              Name(Path\To\ClassName)
              Identifier(ANOTHER_CONSTANT_NAME)
            AST, $this->parseAndPrint('Path\\To\\ClassName::ANOTHER_CONSTANT_NAME'));
    }

    public function testGlobalConstantMask(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode(namespaceOrFullyQualified=false)
              MaskNode(JSON_*)
                Identifier(JSON_)
                WildcardNode(*)
            AST, $this->parseAndPrint('JSON_*'));
    }

    public function testGlobalConstantMaskInANamespace(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode
              MaskNode(JSON_*)
                Identifier(JSON_)
                WildcardNode(*)
              Name(Path\To)
            AST, $this->parseAndPrint('Path\\To\\JSON_*'));
    }

    public function testGlobalConstantMaskMayBeginWithAnAsterisk(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode(namespaceOrFullyQualified=false)
              MaskNode(*_SUFFIX)
                WildcardNode(*)
                Identifier(_SUFFIX)
            AST, $this->parseAndPrint('*_SUFFIX'));
    }

    public function testGlobalConstantMaskOfAWholeNamespace(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode
              MaskNode(*)
                WildcardNode(*)
              Name(Path\To)
            AST, $this->parseAndPrint('Path\\To\\*'));
    }

    public function testClassConstantMask(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
              MaskNode(PREFIX_*)
                Identifier(PREFIX_)
                WildcardNode(*)
            AST, $this->parseAndPrint('Path\\To\\ClassName::PREFIX_*'));
    }

    public function testClassConstantMaskWithoutPrefix(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
              MaskNode(*)
                WildcardNode(*)
            AST, $this->parseAndPrint('Path\\To\\ClassName::*'));
    }

    public function testClassConstantCannotContainNamespace(): void
    {
        $this->expectParsingException('unexpected "\\"');

        $this->parse('ClassName::SOME\\ANY');
    }

    /**
     * A mask standing for every constant there is says nothing worth saying,
     * so at least one segment of a name is required.
     */
    public function testGlobalConstantMaskCannotBeAnAsteriskAlone(): void
    {
        $this->expectParsingException('unexpected end of input');

        $this->parse('*');
    }

    public function testMaskIsMadeOfEverySegmentItIsWrittenOf(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
              MaskNode(PREFIX_*_SUFFIX)
                Identifier(PREFIX_)
                WildcardNode(*)
                Identifier(_SUFFIX)
            AST, $this->parseAndPrint('Path\\To\\ClassName::PREFIX_*_SUFFIX'));
    }

    public function testMaskMayBeginWithAnAsterisk(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
              MaskNode(*_SUFFIX)
                WildcardNode(*)
                Identifier(_SUFFIX)
            AST, $this->parseAndPrint('Path\\To\\ClassName::*_SUFFIX'));
    }

    /**
     * A mask written with no namespace carries the leading separator itself,
     * since there is no name for it to belong to.
     */
    public function testFullyQualifiedGlobalConstantMask(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode(namespaceOrFullyQualified=true)
              MaskNode(JSON_*)
                Identifier(JSON_)
                WildcardNode(*)
            AST, $this->parseAndPrint('\JSON_*'));
    }

    /**
     * The namespace of a mask keeps the separator it is written with, so the
     * name is the one that says the reference is a fully qualified one.
     */
    public function testFullyQualifiedGlobalConstantMaskInANamespace(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode
              MaskNode(JSON_*)
                Identifier(JSON_)
                WildcardNode(*)
              Name(\Path\To)
            AST, $this->parseAndPrint('\Path\To\JSON_*'));
    }

    public function testFullyQualifiedGlobalConstantMaskOfAWholeNamespace(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode
              MaskNode(*)
                WildcardNode(*)
              Name(\Path\To)
            AST, $this->parseAndPrint('\Path\To\*'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, bool}>
     */
    public static function maskQualificationDataProvider(): iterable
    {
        yield 'a mask alone' => ['JSON_*', false];
        yield 'a mask alone behind a separator' => ['\JSON_*', true];
        yield 'a mask in a namespace' => ['Path\To\JSON_*', false];
        yield 'a mask in a namespace behind a separator' => ['\Path\To\JSON_*', true];
        yield 'a namespace whole' => ['Path\To\*', false];
        yield 'a namespace whole behind a separator' => ['\Path\To\*', true];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('maskQualificationDataProvider')]
    public function testAMaskSaysWhetherItIsFullyQualified(string $type, bool $expected): void
    {
        $node = $this->parse($type);

        self::assertInstanceOf(ConstMaskNode::class, $node);
        self::assertSame($expected, $node->isFullyQualified());
        self::assertSame($type, (new \TypeLang\Printer\PrettyTypePrinter())->print($node));
    }
    public function testTwoAsterisksInARowAreNotAMask(): void
    {
        $this->expectParsingException('unexpected "*"');

        $this->parse('Path\\To\\ClassName::PREFIX_**');
    }
}
