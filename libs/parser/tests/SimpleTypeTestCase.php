<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Node\FullQualifiedName;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser')]
class SimpleTypeTestCase extends TestCase
{
    public function testSimpleType(): void
    {
        $type = $this->parse('ExampleName');

        $this->assertTrue($type->name->isSimple);
        $this->assertSame('ExampleName', $type->name->name);
    }

    public function testAllowsDash(): void
    {
        $type = $this->parse('example-name');

        $this->assertTrue($type->name->isSimple);
        $this->assertSame('example-name', $type->name->name);
    }

    public function testNotAllowsDashAtStart(): void
    {
        $this->expectParseError('Syntax error, unrecognized "-"');

        $this->parse('-name');
    }

    public function testAllowsDashAtEnd(): void
    {
        $type = $this->parse('example-');

        $this->assertTrue($type->name->isSimple);
        $this->assertSame('example-', $type->name->name);
    }

    public function testRelativeNamespacedName(): void
    {
        $type = $this->parse('Some\\Any');

        $this->assertFalse($type->name->isSimple);
        $this->assertSame(['Some', 'Any'], $type->name->parts);
        $this->assertSame('Some\\Any', $type->name->name);
    }

    public function testAbsoluteNamespacedName(): void
    {
        $type = $this->parse('\\Some\\Any');

        $this->assertInstanceOf(FullQualifiedName::class, $type->name);

        $this->assertFalse($type->name->isSimple);
        $this->assertSame(['Some', 'Any'], $type->name->parts);
        $this->assertSame('Some\\Any', $type->name->name);
    }

    public function testNamespacedNameWithDash(): void
    {
        $type = $this->parse('Some-Any\\Any');

        $this->assertFalse($type->name->isSimple);
        $this->assertSame(['Some-Any', 'Any'], $type->name->parts);
        $this->assertSame('Some-Any\\Any', $type->name->name);
    }

    public function testBackslashAtEnd(): void
    {
        $this->expectParseError('Syntax error, unexpected end of input'); // Expects continuation

        $this->parse('Some\\Any\\');
    }

    public function testMultipleBackslashes(): void
    {
        $this->expectParseError('Syntax error, unexpected "\"');

        $this->parse('Some\\\\Any');
    }

    public function testMultipleBackslashesAtEnd(): void
    {
        $this->expectParseError('Syntax error, unexpected "\"');

        $this->parse('Some\\Any\\\\');
    }

    public function testMultipleBackslashesAtStart(): void
    {
        $this->expectParseError('Syntax error, unexpected "\"');

        $this->parse('\\\\Some\\Any');
    }
}
