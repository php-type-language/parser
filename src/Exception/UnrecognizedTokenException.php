<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;

final class UnrecognizedTokenException extends ParsingException
{
    /**
     * Occurs when unable to recognize tokens in source code.
     */
    public static function becauseTokenIsUnrecognized(ReadableInterface $source, TokenInterface $token): self
    {
        $message = \vsprintf('Syntax error, unexpected %s in %s', [
            self::printToken($token),
            self::printSource($source),
        ]);

        return new self($message, $source, $token);
    }
}
