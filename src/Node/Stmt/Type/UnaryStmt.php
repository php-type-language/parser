<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
abstract class UnaryStmt extends TypeStatement
{
    public function __construct(
        public readonly TypeStatement $type,
    ) {}
}
