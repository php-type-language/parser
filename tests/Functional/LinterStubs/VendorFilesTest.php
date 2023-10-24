<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Functional\LinterStubs;

class VendorFilesTest extends LinterStubsTestCase
{
    protected static function getCachePathname(): string
    {
        return self::getVendorDirectory() . '/.vendor.cache.php';
    }

    protected static function getFilesDirectory(): string
    {
        return self::getVendorDirectory();
    }
}
