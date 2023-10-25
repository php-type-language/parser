<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Node\Type\TypeStatement;

interface ParserInterface
{
    /**
     * @param resource|string|\SplFileInfo|ReadableInterface $source
     *
     * @return TypeStatement|null
     */
    public function parse(mixed $source): ?TypeStatement;
}
