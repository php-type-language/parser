<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 *
 * @deprecated Since 1.4.3, please use {@see GenericTypeNode} instead.
 */
abstract class GenericTypeStmt extends TypeStatement
{
    /**
     * @param T $type
     */
    public function __construct(
        public TypeStatement $type,
    ) {}
}
