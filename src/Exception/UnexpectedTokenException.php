<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;

final class UnexpectedTokenException extends ParsingException
{
    /**
     * Occurs when a known token is found in an unexpected source location.
     *
     * The message is the one the grammar carries, whether it is written in
     * an "@error" directive or worded by the parser itself.
     */
    public static function becauseTokenIsUnexpected(
        string $message,
        ReadableInterface $source,
        TokenInterface $token,
    ): self {
        $message = \vsprintf('%s in %s', [
            $message,
            self::printSource($source),
        ]);

        return new self($message, $source, $token);
    }
}
