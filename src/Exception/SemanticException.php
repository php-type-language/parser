<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

/**
 * Error occurring while validating the semantics of a syntactically correct
 * type statement.
 */
abstract class SemanticException extends \LogicException implements ParserExceptionInterface
{
    final public const int ERROR_CODE_SHAPE_KEY_DUPLICATION = 0x01;

    final public const int ERROR_CODE_SHAPE_KEY_MIX = 0x02;

    final public const int ERROR_CODE_VARIADIC_WITH_DEFAULT = 0x03;

    final public const int ERROR_CODE_VARIADIC_ALREADY_VARIADIC = 0x04;

    final public const int ERROR_CODE_INVALID_OPERATOR = 0x05;

    protected const int CODE_LAST = self::ERROR_CODE_INVALID_OPERATOR;

    /**
     * @param int<0, max> $offset
     */
    final public function __construct(
        public readonly int $offset,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int<0, max>
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
