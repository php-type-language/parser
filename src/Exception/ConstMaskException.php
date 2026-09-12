<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class ConstMaskException extends SemanticException
{
    /**
     * Occurs when a constant mask is followed by anything at all.
     *
     * @param int<0, max> $offset
     */
    public static function becauseNothingFollowsAMask(
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                'Constant mask is a type entire and cannot be followed by anything',
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
