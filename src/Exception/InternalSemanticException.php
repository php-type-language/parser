<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class InternalSemanticException extends SemanticException
{
    /**
     * Occurs when the grammar builds a sub-node the reducer knows nothing of.
     *
     * @param int<0, max> $offset
     */
    public static function becauseSubNodeIsUnexpected(
        string $type,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf('Internal error, unexpected square bracket sub-node %s', $type),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
