<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Stmt;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class NullableTypeStmt extends TypeStmt
{
    /**
     * @param TypeStmt $stmt
     *
     * @return static
     */
    public static function fromTypeStmt(TypeStmt $stmt): self
    {
        return new static($stmt->offset, $stmt->name, $stmt->args);
    }
}
