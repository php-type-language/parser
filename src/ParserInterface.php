<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * @property-read int<0, max> $lastProcessedTokenOffset
 */
interface ParserInterface
{
    /**
     * Parses variadic sources into an abstract source tree (AST) node.
     *
     * @throws ParserExceptionInterface in case of parsing exception occurs
     * @throws \Throwable in case of internal error occurs
     */
    public function parse(mixed $source): TypeStatement;
}
