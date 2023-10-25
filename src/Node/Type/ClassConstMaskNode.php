<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Type;

use TypeLang\Parser\Node\Identifier;
use TypeLang\Parser\Node\Name;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class ClassConstMaskNode extends TypeStatement
{
    public function __construct(
        public readonly Name $class,
        public readonly ?Identifier $constant = null,
    ) {}
}
