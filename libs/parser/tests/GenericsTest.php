<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Node\Stmt\NamedTypeStmt;
use Hyper\Parser\Node\Template\Parameters;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser')]
class GenericsTest extends TestCase
{
    public function testParameters(): void
    {
        $type = $this->parse('array<a,b,c>');

        $this->assertInstanceOf(Parameters::class, $type->parameters);
        $this->assertCount(3, $type->parameters->list);
    }

    public function testOneParameter(): void
    {
        $type = $this->parse('array<int>');

        $this->assertNotNull($type->parameters);

        $parameters = $type->parameters->list;
        $this->assertCount(1, $parameters);

        $first = $parameters[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $first);
        $this->assertSame('int', $first->name->name);
    }

    public function testManyParameters(): void
    {
        $type = $this->parse('array<int, string>');

        $this->assertNotNull($type->parameters);

        $parameters = $type->parameters->list;
        $this->assertCount(2, $parameters);

        $first = $parameters[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $first);
        $this->assertSame('int', $first->name->name);

        $second = $parameters[1]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $second);
        $this->assertSame('string', $second->name->name);
    }

    public function testNestedGeneric(): void
    {
        $type = $this->parse('array<Some\Any<int, string>>');

        $this->assertNotNull($type->parameters);

        $rootParameters = $type->parameters->list;
        $this->assertCount(1, $rootParameters);

        $rootValue = $rootParameters[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $rootValue);
        $this->assertSame('Some\Any', $rootValue->name->name);

        $nestedParameters = $rootValue->parameters->list;
        $this->assertCount(2, $nestedParameters);

        $nestedValue1 = $nestedParameters[0]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $nestedValue1);
        $this->assertSame('int', $nestedValue1->name->name);

        $nestedValue2 = $nestedParameters[1]->value;
        $this->assertInstanceOf(NamedTypeStmt::class, $nestedValue2);
        $this->assertSame('string', $nestedValue2->name->name);
    }
}
