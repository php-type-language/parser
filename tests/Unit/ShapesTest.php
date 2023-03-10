<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests\Unit;

use Hyper\Parser\Node\Stmt\NamedTypeStmt;
use Hyper\Parser\Node\Stmt\Shape\Argument;
use Hyper\Parser\Node\Stmt\Shape\Arguments;
use Hyper\Parser\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser'), Group('unit')]
class ShapesTest extends TestCase
{
    public function testArguments(): void
    {
        /** @var NamedTypeStmt $type */
        $type = $this->parse('array{a,b,c}');

        $this->assertInstanceOf(Arguments::class, $type->arguments);
        $this->assertTrue($type->arguments->sealed);
        $this->assertCount(3, $type->arguments->list);
    }

    public function testEmptyShape(): void
    {
        /** @var NamedTypeStmt $type */
        $type = $this->parse('array{}');

        $this->assertInstanceOf(Arguments::class, $type->arguments);
        $this->assertTrue($type->arguments->sealed);
        $this->assertCount(0, $type->arguments->list);
    }

    public function testUnsealedArguments(): void
    {
        /** @var NamedTypeStmt $type */
        $type = $this->parse('array{a,b,c,...}');

        $this->assertInstanceOf(Arguments::class, $type->arguments);
        $this->assertFalse($type->arguments->sealed);
        $this->assertCount(3, $type->arguments->list);
    }

    public function testOneAnonymousArgument(): void
    {
        $type = $this->parse('array{int}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(1, $arguments);

        /** @var Argument $first */
        $first = $arguments[0];
        $this->assertNull($first->name);
        $this->assertFalse($first->optional);

        $value = $first->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $value);
        $this->assertSame('int', $value->name->name);
    }

    public function testManyAnonymousArguments(): void
    {
        $type = $this->parse('array{int, string}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(2, $arguments);

        $first = $arguments[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $first);
        $this->assertSame('int', $first->name->name);

        $second = $arguments[1]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $second);
        $this->assertSame('string', $second->name->name);
    }

    public function testNestedAnonymousArguments(): void
    {
        $type = $this->parse('array{Some\Any{int, string}}');

        $this->assertNotNull($type->arguments);

        $rootArguments = $type->arguments->list;
        $this->assertCount(1, $rootArguments);

        $rootValue = $rootArguments[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $rootValue);
        $this->assertSame('Some\Any', $rootValue->name->name);

        $nestedArguments = $rootValue->arguments->list;
        $this->assertCount(2, $nestedArguments);

        $nestedValue1 = $nestedArguments[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $nestedValue1);
        $this->assertSame('int', $nestedValue1->name->name);

        $nestedValue2 = $nestedArguments[1]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $nestedValue2);
        $this->assertSame('string', $nestedValue2->name->name);
    }

    public function testNamedArgument(): void
    {
        $type = $this->parse('array{name:int}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(1, $arguments);

        /** @var Argument $first */
        $first = $arguments[0];
        $this->assertSame('name', $first->name->value);
        $this->assertFalse($first->optional);

        $value = $first->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $value);
        $this->assertSame('int', $value->name->name);
    }

    public function testMixedArguments(): void
    {
        $type = $this->parse('array{some, required:a, optional?:b}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(3, $arguments);

        $this->assertNull($arguments[0]->name);
        $this->assertFalse($arguments[0]->optional);
        $this->assertSame('required', $arguments[1]->name->value);
        $this->assertFalse($arguments[1]->optional);
        $this->assertSame('optional', $arguments[2]->name->value);
        $this->assertTrue($arguments[2]->optional);
    }
}
