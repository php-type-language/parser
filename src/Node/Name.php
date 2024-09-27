<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node;

/**
 * @template-implements \IteratorAggregate<array-key, Identifier>
 */
class Name extends Node implements \IteratorAggregate, \Countable, \Stringable
{
    /**
     * @var non-empty-string
     */
    private const NAMESPACE_DELIMITER = '\\';

    /**
     * @var non-empty-list<Identifier>
     */
    public readonly array $parts;

    /**
     * @param iterable<array-key, Identifier|non-empty-string>|non-empty-string|Identifier $name
     */
    final public function __construct(iterable|string|Identifier $name)
    {
        $parts = $this->parseName($name);

        assert($parts !== [], new \InvalidArgumentException('Name parts count can not be empty'));

        $this->parts = $parts;
    }

    /**
     * @param iterable<array-key, Identifier|non-empty-string>|non-empty-string|Identifier $name
     *
     * @return list<Identifier>
     */
    private function parseName(iterable|string|Identifier $name): array
    {
        if (\is_string($name)) {
            $name = \array_filter(\explode('\\', $name), static fn(string $chunk): bool => $chunk !== '');
        }

        if (\is_iterable($name)) {
            $result = [];

            foreach ($name as $chunk) {
                $result[] = $this->parseChunk($chunk);
            }

            return $result;
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

    public function slice(int $offset = 0, ?int $length = null): self
    {
        return new static(\array_slice($this->parts, $offset, $length));
    }

    /**
     * Appends the passed {@see Name} to the existing one at the end.
     *
     * ```php
     *  $name = new Name('Some\Any');
     *
     *  echo $name->withAdded(new Name('Test\Class'));
     *  > "Some\Any\Test\Class"
     *
     *  echo $name->withAdded(new Name('Any\Class'));
     *  > "Some\Any\Any\Class"
     * ```
     */
    public function withAdded(self $name): self
    {
        return new static([
            ...$this->parts,
            ...$name->parts,
        ]);
    }

    /**
     * Combines two names into one (in case the last one is an alias).
     *
     * ```php
     *   $name = new Name('Some\Any');
     *
     *   echo $name->mergeWith(new Name('Test\Class'));
     *   > "Some\Any\Class"
     *
     *   echo $name->mergeWith(new Name('Any\Class'));
     *   > "Some\Any\Class"
     * ```
     *
     * Real world use case:
     * ```php
     *  // use TypeLang\Parser\Node;
     *  // echo Node::class;
     *
     *  $name = new Name('TypeLang\Parser\Node');
     *  echo $name->mergeWith(new Name('Node'));
     *
     *  // > TypeLang\Parser\Node
     * ```
     *
     * Or aliased:
     * ```php
     *  // use TypeLang\Parser\Exception as Error;
     *  // echo Error\SemanticException::class;
     *
     *  $name = new Name('TypeLang\Parser\Exception');
     *  echo $name->mergeWith(new Name('Error\SemanticException'));
     *
     *  // > TypeLang\Parser\Exception\SemanticException
     * ```
     */
    public function mergeWith(self $name): self
    {
        return new static([
            ...$this->parts,
            ...\array_slice($name->parts, 1),
        ]);
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

    /**
     * @return array{int<0, max>, non-empty-list<Identifier>}
     */
    public function __serialize(): array
    {
        return [$this->offset, $this->parts];
    }

    /**
     * @param array{0?: int<0, max>, 1?: non-empty-list<Identifier>} $data
     *
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->offset = $data[0] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Name offset',
        );

        $this->parts = $data[1] ?? throw new \UnexpectedValueException(
            message: 'Unable to unserialize Name identifier parts',
        );
    }
}
