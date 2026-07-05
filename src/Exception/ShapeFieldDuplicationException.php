<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class ShapeFieldDuplicationException extends SemanticException
{
    /**
     * Occurs when a shape declares the same key more than once.
     *
     * @param non-empty-string $key
     * @param int<0, max> $offset
     */
    public static function becauseShapeFieldIsDuplicated(string $key, int $offset = 0): self
    {
        $message = \sprintf('Duplicate key "%s"', $key);

        return new self($offset, $message, self::ERROR_CODE_SHAPE_KEY_DUPLICATION);
    }
}
