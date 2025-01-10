<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends GenericTypeStmt<TypeStatement>
 */
class NullableTypeNode extends GenericTypeStmt {}
