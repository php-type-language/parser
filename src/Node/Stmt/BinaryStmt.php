<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class BinaryStmt extends Statement
{
    public function __construct(
        public readonly Statement $a,
        public readonly Statement $b,
    ) {
    }
}
