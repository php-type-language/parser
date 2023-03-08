<?php

declare(strict_types=1);

namespace Hyper\DSL\Exception;

use Hyper\Type\Repository\Exception\RegistrationException as BaseRegistrationException;

class RegistrationException extends BaseRegistrationException implements DSLExceptionInterface
{
    protected const CODE_RESERVED_LITERAL = parent::CODE_LAST + 0x01;

    protected const CODE_LAST = parent::CODE_LAST + 0x01;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $purpose
     *
     * @return static
     */
    public static function fromReservedLiteral(string $name, string $purpose): self
    {
        $message = 'Can not register type alias "%s": The given name is reserved for a %s literal';

        return new static(\sprintf($message, $name, $purpose), self::CODE_RESERVED_LITERAL);
    }
}
