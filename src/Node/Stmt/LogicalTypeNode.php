<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement
 * @template-implements \IteratorAggregate<array-key, T>
 */
abstract class LogicalTypeNode extends TypeStatement implements \IteratorAggregate, \Countable
{
    /**
     * @var non-empty-list<T>
     */
    public array $statements;

    public function __construct(
        TypeStatement $a,
        TypeStatement $b,
        TypeStatement ...$other,
    ) {
        /**
         * @psalm-suppress PropertyTypeCoercion
         * @psalm-suppress ArgumentTypeCoercion
         */
        $this->statements = [...$this->unwrap([$a, $b, ...$other])];
    }

    /**
     * @param list<TypeStatement> $statements
     *
     * @return iterable<array-key, TypeStatement>
     */
    private function unwrap(array $statements): iterable
    {
        foreach ($statements as $statement) {
            if ($statement instanceof static) {
                yield from $this->unwrap($statement->statements);
            } else {
                yield $statement;
            }
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->statements);
    }

    /**
     * @return int<2, max> A logical statement must contain at least 2 elements.
     */
    public function count(): int
    {
        /** @var int<2, max> */
        return \count($this->statements);
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this->statements as $statement) {
            $items[] = $statement->toArray();
        }

        return [
            ...parent::toArray(),
            'items' => $items,
        ];
    }

    public function __serialize(): array
    {
        return [$this->offset, $this->statements];
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function __unserialize(array $data): void
    {
        $this->offset = $data[0] ?? throw new \UnexpectedValueException(\sprintf(
            'Unable to unserialize %s offset',
            static::class,
        ));

        $this->statements = $data[1] ?? throw new \UnexpectedValueException(\sprintf(
            'Unable to unserialize %s statements',
            static::class,
        ));
    }
}
