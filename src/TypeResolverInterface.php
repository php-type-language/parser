<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

interface TypeResolverInterface
{
    /**
     * Replaces all {@see Name} occurrences in AST with the required ones.
     *
     * In case of "$transform" callback returns {@see null}, then skips the
     * replacement.
     *
     * @template TArgType of TypeNode
     * @param TArgType $type
     * @param callable(Name):(Name|null) $transform
     * @return TArgType
     */
    public function resolve(TypeNode $type, callable $transform): TypeNode;
}
