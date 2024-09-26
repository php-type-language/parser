<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Attribute;

use PhpParser\Node\AttributeGroup;
use TypeLang\Parser\Node\NodeList;

/**
 * @template-extends NodeList<AttributeGroup>
 */
final class AttributeGroupsListNode extends NodeList {}
