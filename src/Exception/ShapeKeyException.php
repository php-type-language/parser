<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class ShapeKeyException extends SemanticException
{
    /**
     * Occurs when a shape key is written as something else than a name.
     *
     * @param int<0, max> $offset
     */
    public static function becauseKeyIsNotAName(
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                'Shape key must be a name, a number, a string or a reference to a constant',
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
