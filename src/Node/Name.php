<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @template-extends \IteratorAggregate<array-key, Identifier>
 */
class Name extends Node implements \IteratorAggregate, \Countable
{
    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    /**
     * @var non-empty-list<Identifier>
     */
    private readonly array $parts;

    /**
     * @param iterable<array-key, Identifier|non-empty-string>|non-empty-string|Identifier $name
     */
    final public function __construct(iterable|string|Identifier $name)
    {
        $this->parts = $this->parseName($name);

        assert($this->parts !== [], new \InvalidArgumentException(
            'Name parts count can not be empty',
        ));
    }

    /**
     * @param iterable<array-key, Identifier|non-empty-string>|non-empty-string|Identifier $name
     *
     * @return list<Identifier>
     */
    private function parseName(iterable|string|Identifier $name): array
    {
        if (\is_iterable($name)) {
            return \array_map($this->parseChunk(...), [...$name]);
        }

        return [$this->parseChunk($name)];
    }

    /**
     * @param non-empty-string|Identifier $chunk
     */
    private function parseChunk(string|Identifier $chunk): Identifier
    {
        if (\is_string($chunk)) {
            return new Identifier($chunk);
        }

        return $chunk;
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
        $first = $this->getFirstPart();

        return $this->isSimple() && $first->isSpecial();
    }

    /**
     * Returns {@see true} in case of name contains builtin type name.
     */
    public function isBuiltin(): bool
    {
        $first = $this->getFirstPart();

        return $this->isSimple() && $first->isBuiltin();
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
     * @return non-empty-list<Identifier>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function getPartsAsString(): array
    {
        $result = [];

        foreach ($this->parts as $identifier) {
            $result[] = $identifier->toString();
        }

        return $result;
    }

    public function getFirstPart(): Identifier
    {
        return $this->parts[\array_key_first($this->parts)];
    }

    /**
     * @return non-empty-string
     */
    public function getFirstPartAsString(): string
    {
        $identifier = $this->getFirstPart();

        return $identifier->toString();
    }

    public function getLastPart(): Identifier
    {
        return $this->parts[\array_key_last($this->parts)];
    }

    /**
     * @return non-empty-string
     */
    public function getLastPartAsString(): string
    {
        $identifier = $this->getLastPart();

        return $identifier->toString();
    }

    /**
     * Returns name as string.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return \implode(self::NAMESPACE_DELIMITER, $this->getPartsAsString());
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
     * @return \Traversable<array-key, Identifier>
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
