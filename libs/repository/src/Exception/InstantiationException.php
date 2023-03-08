<?php

declare(strict_types=1);

namespace Hyper\Repository\Exception;

class InstantiationException extends \OutOfRangeException implements TypeExceptionInterface
{
    final protected const CODE_EMPTY_NAME = 0x01;

    final protected const CODE_INVALID_NAME = 0x02;

    protected const CODE_LAST = self::CODE_INVALID_NAME;

    final public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromEmptyName(): static
    {
        return new static('Type name may not be empty', self::CODE_EMPTY_NAME);
    }

    /**
     * @param non-empty-string $name
     * @param array<non-empty-string> $available
     */
    public static function fromUndefinedType(string $name, array $available = []): static
    {
        $message = \sprintf('"%s" is not a type class or has not been registered', $name);

        if (($similar = self::similar($name, $available)) !== null) {
            $message .= \sprintf(', did you mean "%s"?', $similar);
        }

        throw new static($message, self::CODE_INVALID_NAME);
    }

    /**
     * @param non-empty-string $name
     * @param array<non-empty-string> $available
     *
     * @return non-empty-string|null
     */
    protected static function similar(string $name, array $available): ?string
    {
        $priorities = [];

        if ($available === []) {
            return null;
        }

        foreach ($available as $alias) {
            $priorities[$alias] = \levenshtein($name, $alias);
        }

        \asort($priorities);

        if ($priorities[$key = \array_key_first($priorities)] <= 5) {
            return $key;
        }

        return null;
    }
}
