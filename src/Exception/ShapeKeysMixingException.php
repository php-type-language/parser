<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class ShapeKeysMixingException extends SemanticException
{
    /**
     * Occurs when a shape mixes explicit and implicit keys.
     *
     * @param int<0, max> $offset
     */
    public static function becauseShapeKeysAreMixed(int $offset = 0): self
    {
        $message = 'Cannot mix explicit and implicit shape keys';

        return new self($offset, $message, self::ERROR_CODE_SHAPE_KEY_MIX);
    }
}
