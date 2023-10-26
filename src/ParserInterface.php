<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\ReadableInterface;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Node\Definition\DefinitionStatement;
use TypeLang\Parser\Node\Type\TypeStatement;

interface ParserInterface
{
    /**
     * Parses variadic sources into an abstract source tree (AST) node.
     *
     * @param resource|string|\SplFileInfo|ReadableInterface $source
     *
     * @return iterable<array-key, TypeStatement|DefinitionStatement>
     *
     * @throws ParserExceptionInterface In case of parsing exception occurs.
     * @throws \Throwable In case of internal error occurs.
     */
    public function parse(#[Language('PHP')] mixed $source): iterable;

    /**
     * Parses variadic sources with type statement into an abstract source
     * tree (AST) node.
     *
     * @param resource|string|\SplFileInfo|ReadableInterface $source
     *
     * @throws ParserExceptionInterface In case of parsing exception occurs.
     * @throws \Throwable In case of internal error occurs.
     */
    public function parseType(#[Language('PHP')] mixed $source): ?TypeStatement;
}
