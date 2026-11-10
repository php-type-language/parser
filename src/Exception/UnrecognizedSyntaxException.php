<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Internal\TokenDescriber;

/**
 * The source has a fragment that is no part of the type syntax at all.
 *
 * ```
 *  $parser->parse('int ~ string');
 *  // => UnrecognizedSyntaxException: Unrecognized syntax "~"
 * ```
 */
final class UnrecognizedSyntaxException extends SyntaxErrorException
{
    public static function becauseFragmentIsNotRecognized(
        ReadableInterface $source,
        TokenInterface $token,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('Unrecognized syntax %s', TokenDescriber::describeToken($token));

        return new self(ParsingErrorRange::fromToken($token), $source, $message, 0, $previous);
    }
}
