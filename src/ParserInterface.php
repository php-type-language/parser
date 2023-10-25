<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Type\TypeStatement;

interface ParserInterface
{
    /**
     * @param resource|string|\SplFileInfo $source
     *
     * @return TypeStatement|null
     */
    public function parse(mixed $source): ?TypeStatement;
}
