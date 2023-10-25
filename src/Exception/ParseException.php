<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

class ParseException extends \LogicException implements ParserExceptionInterface
{
    final protected const CODE_UNEXPECTED_TOKEN = 0x01;

    final protected const CODE_UNRECOGNIZED_TOKEN = 0x02;

    final protected const CODE_UNEXPECTED_SYNTAX_ERROR = 0x03;

    final protected const CODE_LOGIC_ERROR = 0x04;

    final protected const CODE_INTERNAL_ERROR = 0x05;

    public const CODE_LAST = self::CODE_INTERNAL_ERROR;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * This error occurs when a known token is found in an
     * unexpected source location.
     *
     * @param int<0, max> $offset
     */
    public static function fromUnexpectedToken(string $char, string $statement, int $offset): static
    {
        $message = \vsprintf('Syntax error, unexpected %s in %s %s', [
            Formatter::token($char),
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::CODE_UNEXPECTED_TOKEN);
    }

    /**
     * This error occurs when unable to recognize tokens in source code.
     *
     * @param int<0, max> $offset
     */
    public static function fromUnrecognizedToken(string $token, string $statement, int $offset): static
    {
        $message = \vsprintf('Syntax error, unrecognized %s in %s %s', [
            Formatter::token($token),
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::CODE_UNRECOGNIZED_TOKEN);
    }

    /**
     * @param int<0, max> $offset
     */
    public static function fromUnrecognizedSyntaxError(string $statement, int $offset): static
    {
        $message = \vsprintf('Internal syntax error, in %s %s', [
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::CODE_UNEXPECTED_SYNTAX_ERROR);
    }

    /**
     * @param int<0, max> $offset
     */
    public static function fromSemanticError(string $message, string $statement, int $offset): static
    {
        $message = \vsprintf('Semantic error, %s in %s %s', [
            \lcfirst($message),
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::CODE_LOGIC_ERROR);
    }

    public static function fromInternalError(string $statement, \Throwable $e): static
    {
        $message = "An internal error occurred while parsing %s";
        $message = \sprintf($message, Formatter::source($statement));

        return new static($message, self::CODE_INTERNAL_ERROR, $e);
    }
}
