<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit;

use TypeLang\Parser\Tests\TestCase;

class NamesTypeTest extends TestCase
{
    public function testSimpleType(): void
    {
        $this->assertTypeStatementSame('ExampleName', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(ExampleName)
            OUTPUT);
    }

    public function testAllowsDash(): void
    {
        $this->assertTypeStatementSame('example-name', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(example-name)
            OUTPUT);
    }

    public function testNotAllowsDashAtStart(): void
    {
        $this->assertTypeStatementFails('-name', <<<'ERROR'
            Syntax error, unrecognized "-"
            ERROR);
    }

    public function testAllowsDashAtEnd(): void
    {
        $this->assertTypeStatementSame('example-', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(example-)
            OUTPUT);
    }

    public function testRelativeNamespacedName(): void
    {
        $this->assertTypeStatementSame('Some\\Any', <<<'OUTPUT'
            Type\NamedTypeNode
              Name(Some\Any)
            OUTPUT);
    }

    public function testAbsoluteNamespacedName(): void
    {
        $this->assertTypeStatementSame('\\Some\\Any', <<<'OUTPUT'
            Type\NamedTypeNode
              FullQualifiedName(\Some\Any)
            OUTPUT);
    }

    public function testNamespacedNameWithDash(): void
    {
        $this->assertTypeStatementSame('Some-Any\\Any', <<<'OUTPUT'
            Type\NamedTypeNode
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
