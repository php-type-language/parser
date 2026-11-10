<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Lexer\Token\EndOfInputToken;
use Phplrt\Lexer\Token\Printer\PrettyTokenPrinter;

/**
 * Tells the tokens of the grammar in the words a user of the library reads,
 * instead of the names the grammar gives them.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 */
final class TokenDescriber
{
    /**
     * The number of the expected tokens an error tells apart, the rest of them
     * being counted instead.
     */
    private const MAX_LISTED = 4;

    /**
     * @var list<non-empty-string>
     */
    private const WORDS = [
        'T_IS',
        'T_NOT',
        'T_OF',
        'T_AS',
        'T_SUPER',
        'T_COVARIANT',
        'T_CONTRAVARIANT',
        'T_INVARIANT',
    ];

    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private const LABELS = [
        'T_NAMESPACE' => 'a type name',
        'T_IDENTIFIER' => 'an identifier',
        'T_IDENTIFIER_MASK' => 'an identifier',
        'T_VIRTUAL_NAME' => 'a type name',
        'T_TRUE' => '"true"',
        'T_FALSE' => '"false"',
        'T_NULL' => '"null"',
        'T_THIS' => '"$this"',
        'T_VARIABLE' => 'a variable',
        'T_FLOAT' => 'a float number',
        'T_INT_HEX' => 'an integer',
        'T_INT_BIN' => 'an integer',
        'T_INT_OCT' => 'an integer',
        'T_INT_OCT_LEGACY' => 'an integer',
        'T_INT_DEC' => 'an integer',
        'T_SQ_STRING' => 'a string',
        'T_DQ_STRING' => 'a string',
        'T_IS_NOT' => '"is not"',
        'T_IS' => '"is"',
        'T_NOT' => '"not"',
        'T_OF' => '"of"',
        'T_AS' => '"as"',
        'T_SUPER' => '"super"',
        'T_COVARIANT' => '"covariant"',
        'T_CONTRAVARIANT' => '"contravariant"',
        'T_INVARIANT' => '"invariant"',
        'T_WILDCARD' => '"*"',
        'T_DOUBLE_COLON' => '"::"',
        'T_COLON' => '":"',
        'T_ELLIPSIS' => '"..."',
        'T_ASSIGN' => '"="',
        'T_UNION' => '"|"',
        'T_AMPERSAND' => '"&"',
        'T_QUESTION' => '"?"',
        'T_COMMA' => '","',
        'T_PAREN_OPEN' => '"("',
        'T_PAREN_CLOSE' => '")"',
        'T_ANGLE_OPEN' => '"<"',
        'T_ANGLE_CLOSE' => '">"',
        'T_BRACKET_OPEN' => '"["',
        'T_BRACKET_CLOSE' => '"]"',
        'T_BRACE_OPEN' => '"{"',
        'T_BRACE_CLOSE' => '"}"',
    ];

    /**
     * Quotes the text of the token, the long one being cut.
     */
    public static function describeToken(TokenInterface $token): string
    {
        if ($token instanceof EndOfInputToken) {
            return 'end of input';
        }

        return (new PrettyTokenPrinter())
            ->printValue($token);
    }

    /**
     * Tells what could have been read instead, the first few of the choices
     * being listed and the rest replaced with an ellipsis.
     *
     * @param list<non-empty-string> $expected
     * @return string|null The {@see null} in case of nothing is known
     */
    public static function describeExpected(array $expected): ?string
    {
        $labels = self::createLabels($expected);
        $listed = \implode(', ', \array_slice($labels, 0, self::MAX_LISTED));

        return match (true) {
            $labels === [] => null,
            \count($labels) > self::MAX_LISTED => $listed . ' and other',
            default => $listed,
        };
    }

    /**
     * @param list<non-empty-string> $expected
     * @return list<non-empty-string>
     */
    private static function createLabels(array $expected): array
    {
        $labels = [];

        foreach (self::withoutCoveredWords($expected) as $name) {
            $labels[self::LABELS[$name] ?? $name] = true;
        }

        return \array_keys($labels);
    }

    /**
     * A name covers the contextual words, they are names as well.
     *
     * @param list<non-empty-string> $expected
     * @return list<non-empty-string>
     */
    private static function withoutCoveredWords(array $expected): array
    {
        if (!\in_array('T_IDENTIFIER', $expected, true)) {
            return $expected;
        }

        return \array_values(\array_diff($expected, self::WORDS));
    }
}
