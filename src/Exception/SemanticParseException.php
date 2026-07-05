<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceExceptionInterface;

final class SemanticParseException extends ParseException
{
    /**
     * Occurs when a semantic error is rebased onto the full source and
     * reported to the user with a rendered location.
     *
     * @throws SourceExceptionInterface
     */
    public static function becauseSemanticErrorOccurs(SemanticException $e, ReadableInterface $source): self
    {
        $message = \vsprintf('%s in %s %s', [
            \ucfirst($e->getMessage()),
            Formatter::source($source->getContents()),
            Formatter::suffix($source->getContents(), $e->getOffset()),
        ]);

        return new self($message, self::ERROR_CODE_SEMANTIC_ERROR_BASE + $e->getCode());
    }
}
