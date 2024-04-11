<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

use PHPUnit\Framework\Attributes\Group;

#[Group('functional'), Group('type-lang/parser')]
class PsalmStubsTest extends LinterStubsTestCase
{
    protected static function getStubsDirectory(): string
    {
        return __DIR__ . '/psalm';
    }
}
