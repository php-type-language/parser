<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Serialization;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('unit'), Group('type-lang/parser')]
final class PhpSerializationTest extends SerializationTestCase
{
    public static function nodesWithSuffixesDataProvider(): iterable
    {
        foreach (self::nodesDataProvider() as $name => [$node]) {
            yield $name => [$node, \hash('xxh32', $name) . '.txt'];
        }
    }

    #[DataProvider('nodesDataProvider')]
    public function testPhpSerialization(object $expected): void
    {
        $actual = \unserialize(\serialize($expected));

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('nodesWithSuffixesDataProvider')]
    public function testPhpSerializationSize(object $node, string $suffix): void
    {
        $pathname = $this->getPathname($node, $suffix);

        if (!\is_file($pathname)) {
            \file_put_contents($pathname, \serialize($node));
        }

        $serializedExpected = \trim(\file_get_contents($pathname));
        $serializedActual = \trim(\serialize($node));

        if ($serializedExpected !== $serializedActual) {
            \fwrite(\STDERR, '-- ' . $node::class . " has been changed\n");
            \fwrite(\STDERR, 'Saved: ' . $serializedExpected . "\n");
            \fwrite(\STDERR, 'Actual: ' . $serializedActual . "\n");
        }

        self::assertLessThanOrEqual(
            expected: \strlen($serializedExpected),
            actual: \strlen($serializedActual),
            message: \sprintf(
                'Saved %d bytes, but actual is %d bytes',
                \strlen($serializedExpected),
                \strlen($serializedActual),
            ),
        );

        \file_put_contents($pathname, $serializedActual);
    }
}
