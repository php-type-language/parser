<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * Tests for the integer literal grammar (decimal, binary, octal, hexadecimal).
 */
#[Group('unit'), Group('type-lang/parser')]
final class IntLiteralTest extends SyntaxTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, int}>
     */
    public static function validIntDataProvider(): iterable
    {
        yield 'zero' => ['0', 0];
        yield 'decimal' => ['42', 42];
        yield 'negative decimal' => ['-42', -42];
        yield 'decimal with underscore' => ['1_000_000', 1000000];

        yield 'binary' => ['0b10101101', 173];
        yield 'binary with underscore' => ['0b10_10_11_01', 173];
        yield 'binary uppercase prefix' => ['0B1010', 10];
        yield 'negative binary' => ['-0b1010', -10];

        yield 'octal' => ['0o42', 34];
        yield 'octal uppercase prefix' => ['0O42', 34];
        yield 'legacy octal' => ['042', 34];
        yield 'octal with underscore' => ['0o42_23', 2195];

        yield 'hexadecimal' => ['0xDEAD', 57005];
        yield 'hexadecimal mixed case' => ['0XDeaD', 57005];
        yield 'hexadecimal with underscore' => ['0xDEAD_BEEF', 3735928559];
    }

    #[DataProvider('validIntDataProvider')]
    public function testValidIntegers(string $type, int $expected): void
    {
        $statement = $this->parse($type);

        self::assertInstanceOf(IntLiteralNode::class, $statement);
        self::assertSame($expected, $statement->value);
    }

    public function testBinaryAllowsOnlyZeroAndOne(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('0b101042');
    }

    public function testOctalAllowsOnlyDigitsUpToSeven(): void
    {
        $this->expectParsingException('unexpected "81"');

        $this->parse('0o4281');
    }

    public function testHexadecimalAllowsOnlyHexDigits(): void
    {
        $this->expectParsingException();

        $this->parse('0xHELL');
    }
}
