<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

final class InvalidConditionalOperatorException extends SemanticException
{
    /**
     * Occurs when a condition is written with an operator the grammar knows
     * nothing of.
     *
     * @param int<0, max> $offset
     */
    public static function becauseConditionalOperatorIsInvalid(
        string $operator,
        ReadableInterface $source,
        int $offset = 0,
    ): self {
        return new self(
            self::describe(
                \sprintf('Invalid conditional operator "%s"', $operator),
                $source,
            ),
            $source,
            self::createToken($source, $offset),
        );
    }
}
