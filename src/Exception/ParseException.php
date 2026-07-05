<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

/**
 * Error occurring while parsing a type statement and reported to the user with
 * a rendered message pointing at the failure location.
 */
abstract class ParseException extends \LogicException implements ParserExceptionInterface
{
    final public const int ERROR_CODE_UNEXPECTED_TOKEN = 0x01;

    final public const int ERROR_CODE_UNRECOGNIZED_TOKEN = 0x02;

    final public const int ERROR_CODE_UNEXPECTED_SYNTAX_ERROR = 0x03;

    final public const int ERROR_CODE_INTERNAL_ERROR = 0x05;

    final public const int ERROR_CODE_SEMANTIC_ERROR_BASE = 0x06;

    protected const int CODE_LAST = self::ERROR_CODE_SEMANTIC_ERROR_BASE;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
