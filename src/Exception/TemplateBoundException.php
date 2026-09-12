<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class TemplateBoundException extends SemanticException
{
    /**
     * Occurs when a template parameter is bounded with a word the grammar
     * knows nothing of.
     *
     * @param int<0, max> $offset
     */
    public static function becauseOperatorIsUnknown(
        string $operator,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf(
                    'Template parameter cannot be bounded with "%s", expected one of "of", "as" or "super"',
                    $operator,
                ),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }

    /**
     * Occurs when a template parameter carries the same limit twice.
     *
     * @param int<0, max> $offset
     */
    public static function becauseBoundIsDuplicated(
        string $kind,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf('Template parameter cannot have more than one %s', $kind),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }

    /**
     * Occurs when a template parameter carries a bound behind its default.
     *
     * @param int<0, max> $offset
     */
    public static function becauseDefaultIsNotWrittenLast(
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                'Template parameter default must be written last, since a bound '
                    . 'behind it reads as a bound of the default itself',
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
