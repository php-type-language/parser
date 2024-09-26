<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Template;

use TypeLang\Parser\Node\NodeList;

/**
 * @template T of ArgumentNode
 * @template-extends NodeList<T>
 *
 * @deprecated Since 1.1, please use {@see TemplateArgumentsListNode} instead.
 */
class ArgumentsListNode extends NodeList {}
