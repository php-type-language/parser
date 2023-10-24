<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

class PhpStanStubsTest extends LinterStubsTestCase
{
    protected static function getCachePathname(): string
    {
        return self::getVendorDirectory() . '/.phpstan-1.10.35.cache.php';
    }

    protected static function getFilesDirectory(): string
    {
        return __DIR__ . '/phpstan/1.10.35';
    }
}
