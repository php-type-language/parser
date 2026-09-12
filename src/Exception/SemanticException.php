<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

/**
 * Error occurring while validating the semantics of a syntactically correct
 * type statement.
 */
abstract class SemanticException extends ParsingException
{
    protected static function describe(string $message, ReadableInterface $source): string
    {
        return \sprintf('%s in %s', $message, self::printSource($source));
    }

    /**
     * @return int<0, max>
     */
    public function getOffset(): int
    {
        return $this->token->offset;
    }
}
