<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser'), Group('feature')]
class VendorCompatTest extends TestCase
{
    private const VENDOR_TAGS = [
        'param', 'var', 'return',
        'phpstan-param', 'phpstan-var', 'phpstan-return',
        'psalm-param', 'psalm-var', 'psalm-return',
    ];

    public static function typesDataProvider(): array
    {
        $result = [];
        $blocks = self::fetchDocTypesFromSources(
            sources: self::getSources(__DIR__ . '/../../../../vendor', ['php']),
            tags: self::VENDOR_TAGS,
        );

        foreach ($blocks as $phpdoc => [$file, $position]) {
            $result[$phpdoc] = [$phpdoc, self::location($file, $position)];
        }

        return $result;
    }

    #[DataProvider('typesDataProvider')]
    public function testStubs(string $type, string $location): void
    {
        $this->expectNotToPerformAssertions();

        try {
            $this->parse($type);
        } catch (\Throwable $e) {
            $message = $e->getMessage()
                . "\n  - Definition: $type"
                . "\n  - Source:     $location"
            ;

            $this->markTestIncomplete($message);
        }
    }
}
