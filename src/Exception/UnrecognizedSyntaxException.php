<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;

final class UnrecognizedSyntaxException extends ParsingException
{
    /**
     * Occurs when the parser reaches a state that does not match any known
     * grammar rule.
     */
    public static function becauseSyntaxIsUnrecognized(ReadableInterface $source, TokenInterface $token): self
    {
        $message = \sprintf('Internal syntax error in %s', self::printSource($source));

        return new self($message, $source, $token);
    }
}
