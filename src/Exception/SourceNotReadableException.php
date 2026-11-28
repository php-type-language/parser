<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

/**
 * The source of a type cannot be read: a value of a type the parser does not
 * read, a file that does not exist.
 */
final class SourceNotReadableException extends ParserException
{
    public static function becauseSourceIsNotCreatable(mixed $value, \Throwable $previous): self
    {
        return new self(
            message: \sprintf('Value of type %s cannot be a source of a type', \get_debug_type($value)),
            previous: $previous,
        );
    }

    public static function becauseSourceIsNotReadable(\Throwable $previous): self
    {
        return new self(
            message: \sprintf('The source of a type cannot be read: %s', $previous->getMessage()),
            previous: $previous,
        );
    }
}
