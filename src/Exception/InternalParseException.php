<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\SourceExceptionInterface;

final class InternalParseException extends ParseException
{
    /**
     * Occurs when an unexpected error is raised while parsing a statement.
     */
    public static function becauseInternalErrorOccurs(string $statement, \Throwable $e): self
    {
        $message = \sprintf('An internal error occurred while parsing %s', Formatter::source($statement));

        return new self($message, self::ERROR_CODE_INTERNAL_ERROR, $e);
    }

    /**
     * Occurs when the parser produces no result for a readable statement.
     */
    public static function becauseTypeStatementIsUnreadable(): self
    {
        return new self('Could not read type statement', self::ERROR_CODE_INTERNAL_ERROR);
    }

    /**
     * Occurs when the source content cannot be read.
     */
    public static function becauseSourceIsUnreadable(SourceExceptionInterface $e): self
    {
        return new self($e->getMessage(), self::ERROR_CODE_INTERNAL_ERROR, $e);
    }
}
