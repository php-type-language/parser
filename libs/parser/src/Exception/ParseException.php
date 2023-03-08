<?php

declare(strict_types=1);

namespace Hyper\Parser\Exception;

use Phplrt\Position\Position;

class ParseException extends \LogicException implements ParserExceptionInterface
{
    protected const CODE_UNEXPECTED_TOKEN = 0x01;

    protected const CODE_UNRECOGNIZED_TOKEN = 0x02;

    protected const CODE_UNEXPECTED_SYNTAX_ERROR = 0x03;

    protected const CODE_INSTANTIATION_ERROR = 0x04;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param int<0, max> $offset
     *
     * @return non-empty-string
     */
    private static function suffix(string $expression, int $offset): string
    {
        if (\str_contains($expression, "\n")) {
            $position = Position::fromOffset($expression, $offset);

            return \sprintf('on line %d at column %d', $position->getLine(), $position->getColumn());
        }

        return \sprintf('at column %d', $offset + 1);
    }

    /**
     * @param non-empty-string $char
     * @param int<0, max> $offset
     */
    public static function fromUnexpectedToken(string $char, string $expression, int $offset): static
    {
        $message = \vsprintf('Syntax error, unexpected %s in "%s" %s', [
            self::escapeChar($char),
            self::escapeSource($expression),
            self::suffix($expression, $offset),
        ]);

        return new static($message, self::CODE_UNEXPECTED_TOKEN);
    }

    /**
     * @return non-empty-string
     */
    private static function escapeChar(string $char): string
    {
        return match ($char) {
            "\0", '' => 'end of input',
            '"' => 'quote (")',
            default => \sprintf('"%s"', $char),
        };
    }

    /**
     * @return non-empty-string
     */
    private static function escapeSource(string $expression): string
    {
        if ($expression === '') {
            return '<empty expression>';
        }

        $expression = \str_replace(["\n", "\r", "\0"], ['\n', '\r', '\0'], $expression);

        while (\str_contains($expression, '  ')) {
            $expression = \str_replace('  ', ' ', $expression);
        }

        if (\strlen($expression) > 30) {
            return \substr($expression, 0, 27) . '...';
        }

        return $expression;
    }

    /**
     * @param non-empty-string $char
     * @param int<0, max> $offset
     */
    public static function fromUnrecognizedToken(string $char, string $expression, int $offset): static
    {
        $message = \vsprintf('Syntax error, unrecognized %s in "%s" %s', [
            self::escapeChar($char),
            self::escapeSource($expression),
            self::suffix($expression, $offset),
        ]);

        return new static($message, self::CODE_UNRECOGNIZED_TOKEN);
    }

    /**
     * @param int<0, max> $offset
     */
    public static function fromUnrecognizedSyntaxError(string $expression, int $offset): static
    {
        $message = \vsprintf('Unrecognized syntax error, in "%s" %s', [
            self::escapeSource($expression),
            self::suffix($expression, $offset),
        ]);

        return new static($message, self::CODE_UNEXPECTED_SYNTAX_ERROR);
    }

    public static function fromTypeInstantiationError(string $expression, \Throwable $e): static
    {
        $message = 'An internal error occurred while parsing "%s": %s';
        $message = \sprintf($message, self::escapeSource($expression), $e->getMessage());

        return new static($message, self::CODE_INSTANTIATION_ERROR, $e);
    }
}
