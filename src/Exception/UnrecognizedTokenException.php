<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\SourceExceptionInterface;

final class UnrecognizedTokenException extends ParseException
{
    /**
     * Occurs when unable to recognize tokens in source code.
     *
     * @param int<0, max> $offset
     * @throws SourceExceptionInterface
     */
    public static function becauseTokenIsUnrecognized(string $token, string $statement, int $offset): self
    {
        $message = \vsprintf('Syntax error, unrecognized %s%s %s', [
            Formatter::token($token),
            $token === $statement ? '' : ' in ' . Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new self($message, self::ERROR_CODE_UNRECOGNIZED_TOKEN);
    }
}
