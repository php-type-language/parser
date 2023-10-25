<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

/**
 * @template T of TypeStatement
 */
abstract class GenericTypeStmt extends TypeStatement
{
    /**
     * @param T $type
     */
    public function __construct(
        public readonly TypeStatement $type,
    ) {}
}
