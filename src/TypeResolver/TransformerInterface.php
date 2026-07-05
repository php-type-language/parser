<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Type\Name;

interface TransformerInterface
{
    public function __invoke(Name $name): ?Name;
}
