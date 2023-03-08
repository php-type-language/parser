<?php

declare(strict_types=1);

namespace Hyper\Type\Complex;

use Hyper\Type\ComplexTypeInterface;
use Hyper\Type\IntType;
use Hyper\Type\StringType;
use Hyper\Type\TypeInterface;
use Hyper\Type\Variadic\UnionType;

/**
 * @template-implements \IteratorAggregate<array-key, TypeInterface>
 * @template-implements \ArrayAccess<array-key, TypeInterface>
 */
class Struct implements ComplexTypeInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
    private const MASK_NONE = 0x00;

    private const MASK_INT = 0x01;

    private const MASK_STRING = 0x02;

    private const MASK_ARRAY_KEY = self::MASK_INT | self::MASK_STRING;

    private static ?self $any = null;

    private static ?self $empty = null;

    private ?TypeInterface $key = null;

    /**
     * @param array<array-key, TypeInterface> $fields Exact description of the structure.
     * @param bool $sealed Indicates that the structure cannot be changed.
     */
    public function __construct(
        public readonly array $fields = [],
        public readonly bool $sealed = false,
    ) {
    }

    public static function any(): self
    {
        return self::$any ??= new self(sealed: false);
    }

    public static function empty(): self
    {
        return self::$empty ??= new self(sealed: true);
    }

    public function getKeyType(): TypeInterface
    {
        if ($this->key !== null) {
            return $this->key;
        }

        $types = self::MASK_NONE;

        foreach (array_keys($this->fields) as $key) {
            $types |= \is_int($key) ? self::MASK_INT : self::MASK_STRING;
        }

        if ($types === self::MASK_NONE || ($types & self::MASK_ARRAY_KEY) === self::MASK_ARRAY_KEY) {
            return $this->key = new UnionType(new IntType(), new StringType());
        }

        if (($types & self::MASK_INT) === self::MASK_INT) {
            return $this->key = IntType::getInstance();
        }

        return $this->key = StringType::getInstance();
    }

    public function getValue(): array
    {
        return $this->fields;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->fields);
    }

    public function offsetExists(mixed $offset): bool
    {
        assert(\is_int($offset) || \is_string($offset));

        return isset($this->fields[$offset]);
    }

    public function offsetGet(mixed $offset): TypeInterface
    {
        assert(\is_int($offset) || \is_string($offset));

        return $this->fields[$offset] ?? throw new \OutOfBoundsException('Invalid struct key "' . $offset . '"');
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        assert(\is_int($offset) || \is_string($offset));

        throw new \LogicException(self::class . ' objects are immutable');
    }

    public function offsetUnset(mixed $offset): never
    {
        assert(\is_int($offset) || \is_string($offset));

        throw new \LogicException(self::class . ' objects are immutable');
    }

    public function count(): int
    {
        return \count($this->fields);
    }
}
