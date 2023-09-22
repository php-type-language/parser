<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class LogicException extends \LogicException implements ParserExceptionInterface
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(string $message, public readonly int $offset, int $code = 0, ?\Throwable $previous = null)
    {
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
