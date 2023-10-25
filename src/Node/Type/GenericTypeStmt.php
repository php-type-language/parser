<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

/**
 * @template T of TypeStatement
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
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
