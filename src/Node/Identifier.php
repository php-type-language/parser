<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

final class Identifier extends Node implements \Stringable
{
    /**
     * @var list<non-empty-string>
     */
    private const SPECIAL_CLASS_NAME = [
        'self',
        'parent',
        'static',
    ];

    /**
     * @var list<non-empty-string>
     */
    private const BUILTIN_TYPE_NAME = [
        'mixed',
        'string',
        'int',
        'float',
        'bool',
        'object',
        'array',
        'void',
        'never',
        'callable',
        'iterable',
        'null',
        'true',
        'false'
    ];

    /**
     * @param non-empty-string $value
     *
     * @psalm-suppress RedundantCondition
     */
    public function __construct(
        public readonly string $value,
    ) {
        assert($this->value !== '', new \InvalidArgumentException(
            'Identifier value cannot be empty',
        ));
    }

    /**
     * Returns {@see true} in case of name contains special class reference.
     */
    public function isSpecial(): bool
    {
        return \in_array($this->value, self::SPECIAL_CLASS_NAME, true);
    }

    /**
     * Returns {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        return \in_array($this->value, self::BUILTIN_TYPE_NAME, true);
    }

    /**
     * Returns name as string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Returns lowercased name as string.
     *
     * @return non-empty-lowercase-string
     */
    public function toLowerString(): string
    {
        return \strtolower($this->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
