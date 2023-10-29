<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;

interface ParserInterface
{
    /**
     * Parses variadic sources into an abstract source tree (AST) node.
     *
     * @param resource|string|\SplFileInfo|ReadableInterface $source
     *
     * @throws ParserExceptionInterface In case of parsing exception occurs.
     * @throws \Throwable In case of internal error occurs.
     *
     * @psalm-suppress UndefinedAttributeClass : Optional (builtin) attribute usage
     */
    public function parse(#[Language('PHP')] mixed $source): ?TypeStatement;
}
