<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class VariadicRedefinitionException extends SemanticException
{
    /**
     * Occurs when a parameter is marked as variadic more than once.
     *
     * @param int<0, max> $offset
     */
    public static function becauseVariadicIsRedefined(int $offset = 0): self
    {
        $message = 'Either prefix or postfix variadic syntax should be used, but not both';

        return new self($offset, $message, self::ERROR_CODE_VARIADIC_ALREADY_VARIADIC);
    }
}
