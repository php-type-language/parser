<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt\Shape;

use TypeLang\Parser\Node\NodeList;

/**
 * @template-extends NodeList<FieldNode>
 */
class FieldsListNode extends NodeList implements \Stringable
{
    /**
     * @param list<FieldNode> $list
     */
    public function __construct(
        array $list = [],
        public bool $sealed = true,
    ) {
        parent::__construct($list);
    }

    public function __toString(): string
    {
        return $this->sealed ? 'sealed' : 'unsealed';
    }
}
