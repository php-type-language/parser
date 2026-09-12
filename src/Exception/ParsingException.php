<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\FileInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Token\Token;
use Phplrt\Position\Position;
use Phplrt\Position\PositionFactory;

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
abstract class ParsingException extends ParserException implements ParserRuntimeExceptionInterface
{
    public PositionInterface $position;

    public function __construct(
        string $message,
        ReadableInterface $source,
        public readonly TokenInterface $token,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->position = $this->createPosition($source, $token);

        if ($source instanceof FileInterface) {
            $pathname = \realpath($source->pathname);

            if ($pathname === false) {
                $pathname = \str_replace('\\', '/', $source->pathname);
            }

            $this->file = $pathname;
            $this->line = $this->position->line;
        }

        parent::__construct($message, $source, $code, $previous);
    }

    private function createPosition(ReadableInterface $source, TokenInterface $token): PositionInterface
    {
        try {
            return (new PositionFactory())
                ->createFromOffset($source, $token->offset);
        } catch (\Throwable) {
            return new Position();
        }
    }

    /**
     * @param int<0, max> $offset
     */
    protected static function createToken(ReadableInterface $source, int $offset, int $length = 0): TokenInterface
    {
        return new Token(
            id: 0,
            name: null,
            channel: Channel::Default,
            value: \substr($source->content, $offset, $length),
            offset: $offset,
        );
    }

    private function printPosition(): string
    {
        return \sprintf('on line %d at column %d', $this->position->line, $this->position->column);
    }

    public function __toString(): string
    {
        $message = $this->message;
        $this->message = \sprintf('%s %s', $message, $this->printPosition());

        try {
            return parent::__toString();
        } finally {
            $this->message = $message;
        }
    }
}
