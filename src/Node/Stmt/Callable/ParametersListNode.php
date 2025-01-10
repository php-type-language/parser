<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Callable;

use TypeLang\Parser\Node\NodeList;

/**
 * @template-extends NodeList<ParameterNode>
 *
 * @deprecated Since 1.3, please use {@see CallableParametersListNode} instead.
 */
class ParametersListNode extends NodeList {}
