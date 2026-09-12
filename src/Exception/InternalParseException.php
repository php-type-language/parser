<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class InternalParseException extends ParserException
{
    /**
     * Occurs when an unexpected error is raised while parsing a statement.
     */
    public static function becauseInternalErrorOccurs(ReadableInterface $source, \Throwable $e): self
    {
        $message = \sprintf('An internal error occurred while parsing %s', self::printSource($source));

        return new self($message, $source, 0, $e);
    }
}
