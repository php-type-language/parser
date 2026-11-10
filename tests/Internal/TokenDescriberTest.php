<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Phplrt\Lexer\Token\EndOfInputToken;
use TypeLang\Parser\Internal\TokenDescriber;
use TypeLang\Parser\Tests\TestCase;

#[CoversClass(TokenDescriber::class)]
final class TokenDescriberTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{list<non-empty-string>, string|null}>
     */
    public static function expectedDataProvider(): iterable
    {
        yield 'nothing' => [[], null];
        yield 'a punctuation token' => [['T_PAREN_OPEN'], '"("'];
        yield 'several tokens' => [['T_PAREN_OPEN', 'T_ANGLE_OPEN'], '"(", "<"'];
        yield 'the same label twice' => [['T_INT_DEC', 'T_INT_HEX', 'T_INT_BIN'], 'an integer'];
        yield 'an unknown token name' => [['T_UNKNOWN'], 'T_UNKNOWN'];
        yield 'the contextual words next to a name' => [['T_IDENTIFIER', 'T_IS', 'T_OF'], 'an identifier'];
        yield 'the contextual words alone' => [['T_IS', 'T_OF'], '"is", "of"'];
        yield 'exactly the limit' => [
            ['T_COMMA', 'T_COLON', 'T_ASSIGN', 'T_UNION'],
            '",", ":", "=", "|"',
        ];
        yield 'over the limit' => [
            ['T_COMMA', 'T_COLON', 'T_ASSIGN', 'T_UNION', 'T_QUESTION'],
            '",", ":", "=", "|" and other',
        ];
    }

    /**
     * @param list<non-empty-string> $expected
     */
    #[Test]
    #[DataProvider('expectedDataProvider')]
    public function describesTheExpectedTokens(array $expected, ?string $description): void
    {
        self::assertSame($description, TokenDescriber::describeExpected($expected));
    }

    #[Test]
    public function describesTheEndOfInput(): void
    {
        self::assertSame('end of input', TokenDescriber::describeToken(new EndOfInputToken(3)));
    }
}
