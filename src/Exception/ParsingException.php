<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Exception\ErrorPrinter;
use Phplrt\Position\Position;

abstract class ParsingException extends ParserException implements
    ParserRuntimeExceptionInterface
{
    /**
     * Line and column of the start of the {@see $range}.
     */
    public readonly PositionInterface $position;

    public function __construct(
        public readonly ParsingErrorRange $range,
        public readonly ReadableInterface $source,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);

        $this->position = self::createPosition($range, $source);
    }

    /**
     * Returns the first line and column when the source cannot be read.
     */
    private static function createPosition(ParsingErrorRange $range, ReadableInterface $source): PositionInterface
    {
        try {
            return $range->toStartPosition($source);
        } catch (SourceExceptionInterface) {
            return new Position();
        }
    }

    public function __toString(): string
    {
        try {
            return (string) (new ErrorPrinter())
                ->print($this)
                ->withSource($this->source)
                ->withInterval($this->range->offset, $this->range->length)
            ;
        } catch (\Throwable) {
            return parent::__toString();
        }
    }
}
