<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\ReadableInterface;

abstract class ParserException extends \LogicException implements
    ParserExceptionInterface
{
    public function __construct(
        public readonly ReadableInterface $source,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
