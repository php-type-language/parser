<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;

/**
 * Occurs when the content of a source cannot be read.
 */
final class UnreadableSourceException extends ParserException
{
    public static function becauseSourceIsUnreadable(
        ReadableInterface $source,
        SourceExceptionInterface $e,
    ): self {
        return new self($e->getMessage(), $source, 0, $e);
    }
}
