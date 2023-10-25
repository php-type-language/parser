<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
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
