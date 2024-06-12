<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\SourceExceptionInterface;

class ParseException extends \LogicException implements ParserExceptionInterface
{
    final public const ERROR_CODE_UNEXPECTED_TOKEN = 0x01;

    final public const ERROR_CODE_UNRECOGNIZED_TOKEN = 0x02;

    final public const ERROR_CODE_UNEXPECTED_SYNTAX_ERROR = 0x03;

    final public const ERROR_CODE_INTERNAL_ERROR = 0x05;

    final public const ERROR_CODE_SEMANTIC_ERROR_BASE = 0x06;

    protected const CODE_LAST = self::ERROR_CODE_SEMANTIC_ERROR_BASE;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * This error occurs when a known token is found in an
     * unexpected source location.
     *
     * @param int<0, max> $offset
     *
     * @throws SourceExceptionInterface
     */
    public static function fromUnexpectedToken(string $token, string $statement, int $offset): static
    {
        $message = \vsprintf('Syntax error, unexpected %s%s %s', [
            Formatter::token($token),
            $token === $statement ? '' : ' in ' . Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::ERROR_CODE_UNEXPECTED_TOKEN);
    }

    /**
     * This error occurs when unable to recognize tokens in source code.
     *
     * @param int<0, max> $offset
     *
     * @throws SourceExceptionInterface
     */
    public static function fromUnrecognizedToken(string $token, string $statement, int $offset): static
    {
        $message = \vsprintf('Syntax error, unrecognized %s%s %s', [
            Formatter::token($token),
            $token === $statement ? '' : ' in ' . Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::ERROR_CODE_UNRECOGNIZED_TOKEN);
    }

    /**
     * @param int<0, max> $offset
     *
     * @throws SourceExceptionInterface
     */
    public static function fromUnrecognizedSyntaxError(string $statement, int $offset): static
    {
        $message = \vsprintf('Internal syntax error, in %s %s', [
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::ERROR_CODE_UNEXPECTED_SYNTAX_ERROR);
    }

    /**
     * @param int<0, max> $offset
     *
     * @throws SourceExceptionInterface
     */
    public static function fromSemanticError(string $message, string $statement, int $offset, int $code = 0): static
    {
        $message = \vsprintf('%s in %s %s', [
            \ucfirst($message),
            Formatter::source($statement),
            Formatter::suffix($statement, $offset),
        ]);

        return new static($message, self::ERROR_CODE_SEMANTIC_ERROR_BASE + $code);
    }

    public static function fromInternalError(string $statement, \Throwable $e): static
    {
        $message = 'An internal error occurred while parsing %s';
        $message = \sprintf($message, Formatter::source($statement));

        return new static($message, self::ERROR_CODE_INTERNAL_ERROR, $e);
    }
}
