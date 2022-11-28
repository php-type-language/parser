<?php

declare(strict_types=1);

namespace Hyper\Type\Exception;

class TypeException extends \LogicException implements TypeExceptionInterface
{
    protected const CODE_LAST = 0x00;

    final public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private static function isResource(mixed $value): bool
    {
        return \is_resource($value) || \get_debug_type($value) === 'resource (closed)';
    }

    /**
     * @param mixed $value
     *
     * @return non-empty-string
     */
    protected static function typeToString(mixed $value): string
    {
        if (self::isResource($value)) {
            return 'resource';
        }

        return \get_debug_type($value);
    }

    /**
     * @param mixed $value
     *
     * @return non-empty-string
     */
    protected static function valueToString(mixed $value, int $depth = 0): string
    {
        return match (true) {
            \is_array($value) => $depth === 0
                ? self::arrayToString($value, $depth)
                : 'array(' . \count($value) . ')',
            \is_string($value) => '"' . self::escapeString(\addslashes($value)) . '"',
            \is_int($value),
            \is_float($value) => self::escapeString((string)$value),
            \is_bool($value) => $value ? 'true' : 'false',
            self::isResource($value) => self::resourceToString($value),
            \is_object($value) => 'object<' . $value::class . '>',
            default => \get_debug_type($value),
        };
    }

    /**
     * @param resource $resource
     *
     * @return non-empty-string
     */
    private static function resourceToString(mixed $resource): string
    {
        if (\get_resource_type($resource) === 'Unknown') {
            return 'closed';
        }

        return \get_resource_type($resource);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private static function escapeString(string $value): string
    {
        $length = \strlen($value);
        $result = '';

        for ($i = 0; $i < $length; ++$i) {
            if (\ctype_alnum($value[$i])) {
                $result .= $value[$i];
            } else {
                $result .= '\\x' . \ord($value[$i]);
            }

            if ($i > 40) {
                return $result . '…' . ($length - 40) . '+';
            }
        }

        return $result;
    }

    /**
     * @param array $values
     * @param int<0, max> $depth
     *
     * @return non-empty-string
     */
    private static function arrayToString(array $values, int $depth): string
    {
        $result = [];
        $index = 0;

        foreach ($values as $value) {
            $result[] = self::valueToString($value, $depth + 1);

            if (++$index >= 3) {
                $result[] = '…' . (\count($values) - 3);
                break;
            }
        }

        return '[' . \implode(', ', $result) . ']';
    }
}
