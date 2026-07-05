<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class InvalidConditionalOperatorException extends SemanticException
{
    /**
     * Occurs when a conditional expression uses an unsupported operator.
     *
     * @param non-empty-string $operator
     * @param int<0, max> $offset
     */
    public static function becauseConditionalOperatorIsInvalid(string $operator, int $offset = 0): self
    {
        $message = \sprintf('Invalid conditional operator "%s"', $operator);

        return new self($offset, $message, self::ERROR_CODE_INVALID_OPERATOR);
    }
}
