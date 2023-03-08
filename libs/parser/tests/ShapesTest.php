<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Node\Shape\Argument;
use Hyper\Parser\Node\Shape\NamedArgument;
use Hyper\Parser\Node\Shape\Shape;
use Hyper\Parser\Node\Stmt\NamedType;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser')]
class ShapesTest extends TestCase
{
    public function testArguments(): void
    {
        $type = $this->parse('array{a,b,c}');

        $this->assertInstanceOf(Shape::class, $type->arguments);
        $this->assertCount(3, $type->arguments->list);
    }

    public function testOneAnonymousArgument(): void
    {
        $type = $this->parse('array{int}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(1, $arguments);

        $first = $arguments[0];
        $this->assertInstanceOf(Argument::class, $first);

        $value = $first->value;
        $this->assertInstanceOf(NamedType::class, $value);
        $this->assertSame('int', $value->name->name);
    }

    public function testManyAnonymousArguments(): void
    {
        $type = $this->parse('array{int, string}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(2, $arguments);

        $first = $arguments[0]->value;
        $this->assertInstanceOf(NamedType::class, $first);
        $this->assertSame('int', $first->name->name);

        $second = $arguments[1]->value;
        $this->assertInstanceOf(NamedType::class, $second);
        $this->assertSame('string', $second->name->name);
    }

    public function testNestedAnonymousArguments(): void
    {
        $type = $this->parse('array{Some\Any{int, string}}');

        $this->assertNotNull($type->arguments);

        $rootArguments = $type->arguments->list;
        $this->assertCount(1, $rootArguments);

        $rootValue = $rootArguments[0]->value;
        $this->assertInstanceOf(NamedType::class, $rootValue);
        $this->assertSame('Some\Any', $rootValue->name->name);

        $nestedArguments = $rootValue->arguments->list;
        $this->assertCount(2, $nestedArguments);

        $nestedValue1 = $nestedArguments[0]->value;
        $this->assertInstanceOf(NamedType::class, $nestedValue1);
        $this->assertSame('int', $nestedValue1->name->name);

        $nestedValue2 = $nestedArguments[1]->value;
        $this->assertInstanceOf(NamedType::class, $nestedValue2);
        $this->assertSame('string', $nestedValue2->name->name);
    }

    public function testNamedArgument(): void
    {
        $type = $this->parse('array{name:int}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(1, $arguments);

        $first = $arguments[0];
        $this->assertInstanceOf(NamedArgument::class, $first);
        $this->assertSame('name', $first->name);

        $value = $first->value;
        $this->assertInstanceOf(NamedType::class, $value);
        $this->assertSame('int', $value->name->name);
    }

    public function testMixedArguments(): void
    {
        $type = $this->parse('array{some, __arg:any}');

        $this->assertNotNull($type->arguments);

        $arguments = $type->arguments->list;
        $this->assertCount(2, $arguments);

        $this->assertInstanceOf(Argument::class, $arguments[0]);
        $this->assertInstanceOf(NamedArgument::class, $arguments[1]);
        $this->assertSame('__arg', $arguments[1]->name);
    }
}
