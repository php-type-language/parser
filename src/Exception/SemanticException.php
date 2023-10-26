<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

class SemanticException extends \LogicException implements ParserExceptionInterface
{
    final public const CODE_SHAPE_KEY_DUPLICATION = 0x01;

    final public const CODE_SHAPE_KEY_MIX = 0x02;

    final public const CODE_VARIADIC_WITH_DEFAULT = 0x03;

    public const CODE_LAST = self::CODE_VARIADIC_WITH_DEFAULT;

    /**
     * @param int<0, max> $offset
     */
    final public function __construct(string $message, public readonly int $offset, int $code = 0, ?\Throwable $previous = null)
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
