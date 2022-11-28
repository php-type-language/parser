<?php

declare(strict_types=1);

namespace Hyper\Type\Repository\Exception;

class RegistrationException extends \OutOfRangeException implements TypeExceptionInterface
{
    final protected const CODE_EMPTY_NAME       = 0x01;
    final protected const CODE_EMPTY_ALIAS_NAME = 0x02;

    protected const CODE_LAST = 0x02;

    final public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return static
     */
    public static function fromEmptyName(): static
    {
        return new static('Type name may not be empty', self::CODE_EMPTY_NAME);
    }

    /**
     * @return static
     */
    public static function fromEmptyAliasName(): static
    {
        return new static('Type alias name may not be empty', self::CODE_EMPTY_ALIAS_NAME);
    }
}
