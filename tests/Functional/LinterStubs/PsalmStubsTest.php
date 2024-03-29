<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

use PHPUnit\Framework\Attributes\Group;

#[Group('functional'), Group('type-lang/parser')]
class PsalmStubsTest extends LinterStubsTestCase
{
    protected static function getCachePathname(): string
    {
        return self::getVendorDirectory() . '/.psalm-5.15.0.cache.php';
    }

    protected static function getFilesDirectory(): string
    {
        return __DIR__ . '/psalm/5.15.0';
    }
}
