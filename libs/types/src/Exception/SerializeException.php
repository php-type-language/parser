<?php

declare(strict_types=1);

namespace Hyper\Type\Exception;

class SerializeException extends TypeException
{
    final protected const CODE_SERIALIZE_ERROR = 0x01;

    protected const CODE_LAST = 0x01;

    /**
     * @param non-empty-string $expected
     * @param mixed $given
     *
     * @return static
     */
    public static function fromInvalidType(string $expected, mixed $given): self
    {
        $message = 'Can not serialize %s(%s) because %s is expected';

        return new self(\vsprintf($message, [
            self::typeToString($given),
            self::valueToString($given),
            $expected,
        ]), self::CODE_SERIALIZE_ERROR);
    }
}
