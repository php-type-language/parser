<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;

/**
 * The source breaks a construct of the syntax, which the grammar describes
 * with a message of its own.
 *
 * ```
 *  $parser->parse('(int');
 *  // => MalformedSyntaxException: The group is not closed properly, ")" expected
 * ```
 */
final class MalformedSyntaxException extends UnexpectedTokenException
{
    public static function becauseConstructIsMalformed(
        ReadableInterface $source,
        TokenInterface $token,
        string $message,
        ?\Throwable $previous = null,
    ): self {
        return new self(ParsingErrorRange::fromToken($token), $source, $message, 0, $previous);
    }
}
