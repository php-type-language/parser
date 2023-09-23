<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @template-extends \IteratorAggregate<array-key, non-empty-string>
 */
class Name extends Node implements \IteratorAggregate, \Countable
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
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    /**
     * @var non-empty-list<non-empty-string>
     */
    private readonly array $parts;

    /**
     * @param self|non-empty-string|iterable<array-key, non-empty-string> $name
     */
    final public function __construct(self|string|iterable $name)
    {
        $this->parts = self::parseName($name);

        assert($this->parts !== [], new \InvalidArgumentException(
            'Name parts count can not be empty',
        ));
    }

    /**
     * @param self|non-empty-string|iterable<array-key, non-empty-string> $name
     *
     * @return list<non-empty-string>
     */
    private static function parseName(self|string|iterable $name): array
    {
        return match (true) {
            $name instanceof self => $name->parts,
            \is_string($name) => self::parseString($name),
            default => [...$name],
        };
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-list<non-empty-string>
     */
    private static function parseString(string $name): array
    {
        $parts = \explode(self::NAMESPACE_DELIMITER, $name);
        $parts = \array_map(\trim(...), $parts);

        return \array_filter($parts);
    }

    /**
     * Checks whether the name is simple (unqualified).
     */
    public function isSimple(): bool
    {
        return \count($this->parts) === 1;
    }

    /**
     * Returns {@see true} in case of name is full qualified.
     */
    public function isFullQualified(): bool
    {
        return $this instanceof FullQualifiedName;
    }

    /**
     * Returns {@see true} in case of name contains special class reference.
     */
    public function isSpecial(): bool
    {
        return $this->isSimple()
            && \in_array($this->getFirstPart(), self::SPECIAL_CLASS_NAME, true);
    }

    /**
     * Returns {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        return $this->isSimple()
            && \in_array($this->getFirstPart(), self::BUILTIN_TYPE_NAME, true);
    }

    /**
     * @param int<0, max> $offset
     * @param int<0, max>|null $length
     */
    public function slice(int $offset = 0, int $length = null): self
    {
        return new static(\array_slice($this->parts, $offset, $length));
    }

    /**
     * @param self|non-empty-string|iterable<array-key, non-empty-string> $name
     */
    public function prepend(self|string|iterable $name): self
    {
        return new static([...self::parseName($name), ...$this->parts]);
    }

    /**
     * @param self|non-empty-string|iterable<array-key, non-empty-string> $name
     */
    public function append(self|string|iterable $name): self
    {
        return new static([...$this->parts, ...self::parseName($name)]);
    }

    /**
     * Convert name to full qualified name instance.
     */
    public function toFullQualified(): FullQualifiedName
    {
        if ($this instanceof FullQualifiedName) {
            return clone $this;
        }

        return new FullQualifiedName($this->parts);
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @return non-empty-string
     */
    public function getFirstPart(): string
    {
        return \reset($this->parts);
    }

    /**
     * @return non-empty-string
     */
    public function getLastPart(): string
    {
        return \end($this->parts);
    }

    /**
     * Returns name as string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return \implode(self::NAMESPACE_DELIMITER, $this->getParts());
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

    /**
     * @return \Traversable<array-key, non-empty-string>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->parts);
    }

    /**
     * @return int<1, max>
     */
    public function count(): int
    {
        return \count($this->parts);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
