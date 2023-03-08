<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Exception;

use Hyper\Type\Repository\Exception\InstantiationException as BaseInstantiationException;

class InstantiationException extends BaseInstantiationException implements DSLExceptionInterface
{
    protected const CODE_UNEXPECTED_TOKEN = parent::CODE_LAST + 0x01;

    protected const CODE_UNRECOGNIZED_TOKEN = parent::CODE_LAST + 0x02;

    protected const CODE_UNEXPECTED_SYNTAX_ERROR = parent::CODE_LAST + 0x03;

    protected const CODE_INSTANTIATION_ERROR = parent::CODE_LAST + 0x04;

    protected const CODE_LAST = parent::CODE_LAST + 0x04;

    /**
     * @param non-empty-string $char
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromUnexpectedToken(string $char, string $dsl, int $offset): self
    {
        $message = \vsprintf('Syntax error, unexpected %s in %s at offset %d', [
            self::escapeChar($char),
            self::escapeSource($dsl),
            $offset,
        ]);

        return new static($message, self::CODE_UNEXPECTED_TOKEN);
    }

    /**
     *
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
     * @param non-empty-string $dsl
     *
     * @return non-empty-string
     */
    private static function escapeSource(string $dsl): string
    {
        return $dsl;
    }

    /**
     * @param non-empty-string $char
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromUnrecognizedToken(string $char, string $dsl, int $offset): self
    {
        $message = \vsprintf('Syntax error, unrecognized %s in %s at offset %d', [
            self::escapeChar($char),
            self::escapeSource($dsl),
            $offset,
        ]);

        return new static($message, self::CODE_UNRECOGNIZED_TOKEN);
    }

    /**
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromUnrecognizedSyntaxError(string $dsl, int $offset): self
    {
        $message = \vsprintf('Unrecognized syntax error, in %s at offset %d', [
            self::escapeSource($dsl),
            $offset,
        ]);

        return new static($message, self::CODE_UNEXPECTED_SYNTAX_ERROR);
    }

    /**
     *
     * @return static
     */
    public static function fromTypeInstantiationError(string $dsl, \Throwable $e): self
    {
        $message = 'An error occurred while initializing "%s": %s';
        $message = \sprintf($message, $dsl, $e->getMessage());

        return new static($message, self::CODE_INSTANTIATION_ERROR);
    }
}
