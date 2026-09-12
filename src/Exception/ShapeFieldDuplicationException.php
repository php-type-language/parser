<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class ShapeFieldDuplicationException extends SemanticException
{
    /**
     * Occurs when a shape declares the same key more than once.
     *
     * @param non-empty-string $key
     * @param int<0, max> $offset
     */
    public static function becauseShapeFieldIsDuplicated(
        string $key,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf('Duplicate key "%s"', $key),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
