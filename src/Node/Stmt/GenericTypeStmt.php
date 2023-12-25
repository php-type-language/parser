<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement
 */
abstract class GenericTypeStmt extends TypeStatement
{
    /**
     * @param T $type
     */
    public function __construct(
        public TypeStatement $type,
    ) {}

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'type' => $this->type->toArray(),
        ];
    }
}
