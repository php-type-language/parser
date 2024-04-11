<?php

declare(strict_types=1);

namespace LinterStubs;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Tests\Functional\LinterStubs\LinterStubsTestCase;

#[Group('functional'), Group('type-lang/parser')]
class PhpStormStubsTest extends LinterStubsTestCase
{
    protected static function getStubsDirectory(): string
    {
        return __DIR__ . '/phpstorm';
    }
}
