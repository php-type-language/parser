<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @template T of TypeStatement
 * @template-implements \IteratorAggregate<array-key, T>
 */
abstract class LogicalTypeNode extends TypeStatement implements \IteratorAggregate, \Countable
{
    /**
     * @var non-empty-list<T>
     */
    public readonly array $statements;

    public function __construct(
        TypeStatement $a,
        TypeStatement $b,
        TypeStatement ...$other,
    ) {
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
            yield from $statement instanceof static ? $this->unwrap($statement->statements) : [$statement];
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->statements);
    }

    /**
     * @return int<2, max>
     */
    public function count(): int
    {
        return \count($this->statements);
    }
}
