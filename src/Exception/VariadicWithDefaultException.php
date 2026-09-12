<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class VariadicWithDefaultException extends SemanticException
{
    /**
     * Occurs when a variadic parameter is written with a default.
     *
     * @param int<0, max> $offset
     */
    public static function becauseVariadicHasDefault(
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                'Cannot have variadic param with a default',
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
