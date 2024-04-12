<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Types;

use PHPUnit\Framework\Attributes\Group;

#[Group('unit'), Group('type-lang/parser')]
class SimpleNamedTypesTest extends TypesTestCase
{
    public function testSimpleType(): void
    {
        $this->assertTypeStatementSame('ExampleName', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(ExampleName)
            OUTPUT);
    }

    public function testAllowsDash(): void
    {
        $this->assertTypeStatementSame('example-name', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(example-name)
            OUTPUT);
    }

    public function testNotAllowsDashAtStart(): void
    {
        $this->assertTypeStatementFails('-name', <<<'ERROR'
            Syntax error, unexpected "-"
            ERROR);
    }

    public function testAllowsDashAtEnd(): void
    {
        $this->assertTypeStatementSame('example-', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(example-)
            OUTPUT);
    }

    public function testRelativeNamespacedName(): void
    {
        $this->assertTypeStatementSame('Some\\Any', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(Some\Any)
            OUTPUT);
    }

    public function testAbsoluteNamespacedName(): void
    {
        $this->assertTypeStatementSame('\\Some\\Any', <<<'OUTPUT'
            Stmt\NamedTypeNode
              FullQualifiedName(\Some\Any)
            OUTPUT);
    }

    public function testNamespacedNameWithDash(): void
    {
        $this->assertTypeStatementSame('Some-Any\\Any', <<<'OUTPUT'
            Stmt\NamedTypeNode
              Name(Some-Any\Any)
            OUTPUT);
    }

    public function testBackslashAtEnd(): void
    {
        $this->assertTypeStatementFails('Some\\Any\\', <<<'ERROR'
            Syntax error, unexpected end of input
            ERROR);
    }

    public function testMultipleBackslashes(): void
    {
        $this->assertTypeStatementFails('Some\\\\Any', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }

    public function testMultipleBackslashesAtEnd(): void
    {
        $this->assertTypeStatementFails('Some\\Any\\\\', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }

    public function testMultipleBackslashesAtStart(): void
    {
        $this->assertTypeStatementFails('\\\\Some\\Any', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }
}
