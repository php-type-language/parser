<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

class SemanticException extends \LogicException implements ParserExceptionInterface
{
    final public const ERROR_CODE_SHAPE_KEY_DUPLICATION = 0x01;

    final public const ERROR_CODE_SHAPE_KEY_MIX = 0x02;

    final public const ERROR_CODE_VARIADIC_WITH_DEFAULT = 0x03;

    final public const ERROR_CODE_INVALID_OPERATOR = 0x04;

    protected const CODE_LAST = self::ERROR_CODE_INVALID_OPERATOR;

    /**
     * @param int<0, max> $offset
     */
    final public function __construct(
        public readonly int $offset,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int<0, max>
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param non-empty-string $key
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromShapeFieldDuplication(string $key, int $offset = 0): self
    {
        $message = \sprintf('Duplicate key "%s"', $key);

        return new static($offset, $message, self::ERROR_CODE_SHAPE_KEY_DUPLICATION);
    }

    /**
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromShapeMixedKeys(int $offset = 0): self
    {
        $message = 'Cannot mix explicit and implicit shape keys';

        return new static($offset, $message, self::ERROR_CODE_SHAPE_KEY_MIX);
    }

    /**
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromVariadicWithDefault(int $offset = 0): self
    {
        $message = 'Cannot have variadic param with a default';

        return new static($offset, $message, self::ERROR_CODE_VARIADIC_WITH_DEFAULT);
    }

    /**
     * @param non-empty-string $operator
     * @param int<0, max> $offset
     *
     * @return static
     */
    public static function fromInvalidConditionalOperator(string $operator, int $offset = 0): self
    {
        $message = \sprintf('Invalid conditional operator "%s"', $operator);

        return new static($offset, $message, self::ERROR_CODE_INVALID_OPERATOR);
    }
}
