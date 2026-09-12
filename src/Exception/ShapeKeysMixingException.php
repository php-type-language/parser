<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class ShapeKeysMixingException extends SemanticException
{
    /**
     * Occurs when a shape declares both explicit and implicit keys.
     *
     * @param int<0, max> $offset
     */
    public static function becauseShapeKeysAreMixed(
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                'Cannot mix explicit and implicit shape keys',
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
