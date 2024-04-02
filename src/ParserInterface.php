<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
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
     * @throws ParserExceptionInterface In case of parsing exception occurs.
     * @throws \Throwable In case of internal error occurs.
     *
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): TypeStatement;
}
