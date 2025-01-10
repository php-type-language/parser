<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 */
abstract class GenericTypeStmt extends TypeStatement
{
    /**
     * @param T $type
     */
    public function __construct(
        public TypeStatement $type,
    ) {}

    /**
     * @return array{int<0, max>, T}
     */
    public function __serialize(): array
    {
        return [$this->offset, $this->type];
    }

    /**
     * @param array{0?: int<0, max>, 1?: T} $data
     *
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->offset = $data[0] ?? throw new \UnexpectedValueException(\sprintf(
            'Unable to unserialize %s offset',
            static::class,
        ));

        $this->type = $data[1] ?? throw new \UnexpectedValueException(\sprintf(
            'Unable to unserialize %s type',
            static::class,
        ));
    }
}
