<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\SourceExceptionInterface;

final class UnexpectedTokenException extends ParseException
{
    /**
     * Occurs when a known token is found in an unexpected source location.
     *
     * @param int<0, max> $offset
     * @throws SourceExceptionInterface
     */
    public static function becauseTokenIsUnexpected(string $token, string $statement, int $offset): self
    {
        $message = \vsprintf('Syntax error, unexpected %s%s %s', [
            Formatter::token($token),
            $token === $statement ? '' : ' in ' . Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new self($message, self::ERROR_CODE_UNEXPECTED_TOKEN);
    }
}
