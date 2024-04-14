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
        // @phpstan-ignore-next-line : List of types cannot be empty
        $this->statements = [...$this->unwrap([$a, $b, ...$other])];
    }

    /**
     * @param non-empty-list<TypeStatement> $statements
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

    /**
     * @return array{int<0, max>, non-empty-list<T>}
     */
    public function __serialize(): array
    {
        return [$this->offset, $this->statements];
    }

    /**
     * @param array{0?: int<0, max>, 1?: non-empty-list<T>} $data
     * @throws \UnexpectedValueException
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
