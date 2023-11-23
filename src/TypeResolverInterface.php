<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\TypeStatement;

interface TypeResolverInterface
{
    /**
     * Replaces all {@see Name} occurrences in AST with the required ones.
     *
     * In case of "$transform" callback returns {@see null}, then skips the
     * replacement.
     *
     * @template TArgType of TypeStatement
     *
     * @param TArgType $type
     * @param callable(Name):(Name|null) $transform
     *
     * @return TArgType
     */
    public function resolve(TypeStatement $type, callable $transform): TypeStatement;
}
