<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

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
                Identifier(JSON_THROW_ON_ERROR)
            AST, $this->parseAndPrint('JSON_THROW_ON_ERROR'));
    }

    public function testNamespacedConstantIsInterpretedAsNamedType(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(pcov\version)
                Identifier(pcov)
                Identifier(version)
            AST, $this->parseAndPrint('pcov\\version'));
    }

    public function testClassConstant(): void
    {
        self::assertSame(<<<'AST'
            ClassConstNode
              Name(ClassName)
                Identifier(ClassName)
              Identifier(CONSTANT_NAME)
            AST, $this->parseAndPrint('ClassName::CONSTANT_NAME'));
    }

    public function testNamespacedClassConstant(): void
    {
        self::assertSame(<<<'AST'
            ClassConstNode
              Name(Path\To\ClassName)
                Identifier(Path)
                Identifier(To)
                Identifier(ClassName)
              Identifier(ANOTHER_CONSTANT_NAME)
            AST, $this->parseAndPrint('Path\\To\\ClassName::ANOTHER_CONSTANT_NAME'));
    }

    public function testGlobalConstantMask(): void
    {
        self::assertSame(<<<'AST'
            ConstMaskNode(JSON_*)
              Name(JSON_)
                Identifier(JSON_)
            AST, $this->parseAndPrint('JSON_*'));
    }

    public function testClassConstantMask(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
                Identifier(Path)
                Identifier(To)
                Identifier(ClassName)
              Identifier(PREFIX_)
            AST, $this->parseAndPrint('Path\\To\\ClassName::PREFIX_*'));
    }

    public function testClassConstantMaskWithoutPrefix(): void
    {
        self::assertSame(<<<'AST'
            ClassConstMaskNode
              Name(Path\To\ClassName)
                Identifier(Path)
                Identifier(To)
                Identifier(ClassName)
            AST, $this->parseAndPrint('Path\\To\\ClassName::*'));
    }

    public function testClassConstantCannotContainNamespace(): void
    {
        $this->expectParsingException('unexpected "\\"');

        $this->parse('ClassName::SOME\\ANY');
    }

    public function testGlobalConstantMaskCannotOmitPrefix(): void
    {
        $this->expectParsingException('unexpected "*"');

        $this->parse('*');
    }

    public function testAsteriskMustBeTheFinalCharacter(): void
    {
        $this->expectParsingException('unexpected "_SUFFIX"');

        $this->parse('Path\\To\\ClassName::PREFIX_*_SUFFIX');
    }
}
