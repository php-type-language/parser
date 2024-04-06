<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Serialization;

use JsonSchema\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Parser\Node\Stmt\TypeStatement;

#[Group('unit'), Group('type-lang/parser')]
final class JsonSerializationTest extends SerializationTestCase
{
    public static function nodesWithSuffixesDataProvider(): iterable
    {
        foreach (self::nodesDataProvider() as $name => [$node]) {
            yield $name => [$node, \hash('xxh32', $name) . '.json'];
        }
    }

    #[DataProvider('nodesWithSuffixesDataProvider')]
    public function testJsonSerialization(object $node, string $suffix): void
    {
        $pathname = $this->getPathname($node, $suffix);

        if (!\is_file($pathname)) {
            \file_put_contents(
                filename: $pathname,
                data: \json_encode($node, flags: \JSON_PRETTY_PRINT),
            );
        }

        self::assertSame(
            expected: \file_get_contents($pathname),
            actual: \json_encode($node, flags: \JSON_PRETTY_PRINT),
            message: 'Actual data not compatible with the stored ' . $pathname
        );
    }

    #[DataProvider('typeStatementsDataProvider')]
    public function testJsonSchemaValidation(TypeStatement $stmt): void
    {
        $actual = \json_decode(
            json: $json = \json_encode($stmt, \JSON_PRETTY_PRINT),
            flags: \JSON_THROW_ON_ERROR,
        );
        $schema = \json_decode(\file_get_contents(
            filename: __DIR__. '/../../../resources/schema.json',
        ));

        $validator = new Validator();
        $validator->check($actual, $schema);

        self::assertTrue(
            condition: $validator->isValid(),
            message: '> ' . \implode("\n> ", \array_map(
                callback: fn (array $e): string => $e['message'],
                array: $validator->getErrors(),
            )) . "\n> in $json",
        );
    }
}
