<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\SourceExceptionInterface;

final class UnrecognizedSyntaxException extends ParseException
{
    /**
     * Occurs when the parser reaches a state that does not match any known
     * grammar rule.
     *
     * @param int<0, max> $offset
     * @throws SourceExceptionInterface
     */
    public static function becauseSyntaxIsUnrecognized(string $statement, int $offset): self
    {
        $message = \vsprintf('Internal syntax error, in %s %s', [
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new self($message, self::ERROR_CODE_UNEXPECTED_SYNTAX_ERROR);
    }
}
