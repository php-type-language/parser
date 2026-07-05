<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class VariadicWithDefaultException extends SemanticException
{
    /**
     * Occurs when a variadic parameter also declares a default value.
     *
     * @param int<0, max> $offset
     */
    public static function becauseVariadicHasDefault(int $offset = 0): self
    {
        $message = 'Cannot have variadic param with a default';

        return new self($offset, $message, self::ERROR_CODE_VARIADIC_WITH_DEFAULT);
    }
}
