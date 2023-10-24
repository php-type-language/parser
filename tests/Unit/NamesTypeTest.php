<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class NamesTypeTest extends TestCase
{
    public function testSimpleType(): void
    {
        $this->assertStatementSame('ExampleName', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(ExampleName)
            OUTPUT);
    }

    public function testAllowsDash(): void
    {
        $this->assertStatementSame('example-name', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(example-name)
            OUTPUT);
    }

    public function testNotAllowsDashAtStart(): void
    {
        $this->assertStatementFails('-name', <<<'ERROR'
            Syntax error, unrecognized "-"
            ERROR);
    }

    public function testAllowsDashAtEnd(): void
    {
        $this->assertStatementSame('example-', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(example-)
            OUTPUT);
    }

    public function testRelativeNamespacedName(): void
    {
        $this->assertStatementSame('Some\\Any', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(Some\Any)
            OUTPUT);
    }

    public function testAbsoluteNamespacedName(): void
    {
        $this->assertStatementSame('\\Some\\Any', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              FullQualifiedName(\Some\Any)
            OUTPUT);
    }

    public function testNamespacedNameWithDash(): void
    {
        $this->assertStatementSame('Some-Any\\Any', <<<'OUTPUT'
            Stmt\Type\NamedTypeNode
              Name(Some-Any\Any)
            OUTPUT);
    }

    public function testBackslashAtEnd(): void
    {
        $this->assertStatementFails('Some\\Any\\', <<<'ERROR'
            Syntax error, unexpected end of input
            ERROR);
    }

    public function testMultipleBackslashes(): void
    {
        $this->assertStatementFails('Some\\\\Any', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }

    public function testMultipleBackslashesAtEnd(): void
    {
        $this->assertStatementFails('Some\\Any\\\\', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }

    public function testMultipleBackslashesAtStart(): void
    {
        $this->assertStatementFails('\\\\Some\\Any', <<<'ERROR'
            Syntax error, unexpected "\"
            ERROR);
    }
}
