<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Type;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 *
 * @template T of TypeStatement
 * @template-extends LogicalTypeNode<T>
 */
class IntersectionTypeNode extends LogicalTypeNode {}
