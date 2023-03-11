<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Node\Stmt\Statement;

interface ParserInterface
{
    /**
     * @param resource|string|\SplFileInfo $source
     *
     * @return Statement|null
     */
    public function parse(mixed $source): ?Statement;
}
