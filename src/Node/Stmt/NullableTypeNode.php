<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends GenericTypeNode<TypeStatement>
 */
class NullableTypeNode extends GenericTypeNode {}
