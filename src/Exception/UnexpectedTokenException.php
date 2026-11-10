<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Internal\TokenDescriber;

/**
 * The source has a token, or ends, at the place where the syntax allows
 * another one.
 *
 * ```
 *  $parser->parse('int string');
 *  // => UnexpectedTokenException: Unexpected "string", expected "::" or "<"
 *
 *  $parser->parse('int|');
 *  // => UnexpectedTokenException: Unexpected end of input, expected a name
 * ```
 */
class UnexpectedTokenException extends SyntaxErrorException
{
    /**
     * @param list<non-empty-string> $expected The names of the grammar tokens
     *        that could have stood at this place
     */
    public static function becauseTokenIsUnexpected(
        ReadableInterface $source,
        TokenInterface $token,
        array $expected = [],
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('Unexpected %s', TokenDescriber::describeToken($token));
        $hint = TokenDescriber::describeExpected($expected);

        if ($hint !== null) {
            $message .= \sprintf(', expected %s', $hint);
        }

        return new self(ParsingErrorRange::fromToken($token), $source, $message, 0, $previous);
    }
}
