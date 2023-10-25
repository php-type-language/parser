<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

/**
 * @template T of TypeStatement
 * @template-extends GenericTypeStmt<TypeStatement>
 *
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class NullableTypeNode extends GenericTypeStmt {}
