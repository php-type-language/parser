<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Token\Printer\PrettyTokenPrinter;
use Phplrt\Lexer\Token\Token;

/**
 * ```php
 *  $e->getMessage();
 *  // => Unexpected token end of input, a shape must be closed
 *  //    with a brace "}" in "array{"
 *
 *  (string) $e;
 *  // => Unexpected token end of input, a shape must be closed
 *  //    with a brace "}" in "array{" on line 1 at column 7
 * ```
 */
abstract class ParserException extends \LogicException implements
    ParserExceptionInterface
{
    private static ?PrettyTokenPrinter $printer = null;

    public function __construct(
        string $message,
        public readonly ReadableInterface $source,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * ```
     *  "int" (T_NAME)
     *  end of input
     *  "%" (unknown token)
     * ```
     */
    protected static function printToken(TokenInterface $token): string
    {
        return (self::$printer ??= new PrettyTokenPrinter())
            ->print($token);
    }

    /**
     * ```
     *  "array{a: int}"
     *  "array{a: int, b: str…" (12+)
     *  <empty statement>
     * ```
     */
    protected static function printSource(ReadableInterface $source): string
    {
        $content = \trim($source->content);

        if ($content === '') {
            return '<empty statement>';
        }

        return self::printToken(new Token(
            id: 0,
            name: null,
            channel: Channel::Default,
            value: $content,
        ));
    }
}
