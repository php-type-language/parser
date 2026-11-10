<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Position\PositionFactoryInterface;
use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Position\PositionFactory;

final class ParsingErrorRange
{
    private ?PositionFactoryInterface $positions = null;

    public function __construct(
        /**
         * @var int<0, max>
         */
        public readonly int $offset,
        /**
         * @var int<0, max>
         */
        public readonly int $length = 0,
    ) {}

    public static function fromToken(TokenInterface $token): self
    {
        return new self($token->offset, $token->size);
    }

    /**
     * @param int<0, max> $offset
     * @throws SourceExceptionInterface
     */
    private function toPosition(ReadableInterface $source, int $offset): PositionInterface
    {
        return ($this->positions ??= new PositionFactory())
            ->createFromOffset($source, $offset);
    }

    /**
     * @api
     * @throws SourceExceptionInterface
     */
    public function toStartPosition(ReadableInterface $source): PositionInterface
    {
        return $this->toPosition($source, $this->offset);
    }

    /**
     * @api
     * @throws SourceExceptionInterface
     */
    public function toEndPosition(ReadableInterface $source): PositionInterface
    {
        return $this->toPosition($source, $this->offset + $this->length);
    }
}
