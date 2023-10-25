<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Type\Shape\FieldsListNode;
use TypeLang\Parser\Node\Type\Template\ParametersListNode;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class NamedTypeNode extends TypeStatement
{
    public function __construct(
        public readonly Name $name,
        public readonly ?ParametersListNode $parameters = null,
        public readonly ?FieldsListNode $fields = null,
    ) {}
}
