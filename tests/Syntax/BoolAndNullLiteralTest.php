<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;

/**
 * Tests for the "true", "false" and "null" literal grammar.
 */
#[Group('unit'), Group('type-lang/parser')]
final class BoolAndNullLiteralTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, bool}>
     */
    public static function boolDataProvider(): iterable
    {
        yield 'true' => ['true', true];
        yield 'false' => ['false', false];
        yield 'case insensitive true' => ['TruE', true];
        yield 'case insensitive false' => ['FALSE', false];
        yield 'uppercase true' => ['TRUE', true];
    }

    #[DataProvider('boolDataProvider')]
    public function testBoolLiteral(string $type, bool $expected): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(BoolLiteralNode::class, $statement);
        self::assertSame($expected, $statement->value);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function nullDataProvider(): iterable
    {
        yield 'null' => ['null'];
        yield 'case insensitive null' => ['NulL'];
        yield 'uppercase null' => ['NULL'];
    }

    #[DataProvider('nullDataProvider')]
    public function testNullLiteral(string $type): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(NullLiteralNode::class, $statement);
    }
}
