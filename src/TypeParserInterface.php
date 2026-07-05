<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Type\TypeNode;

/**
 * @template-contravariant TSource of mixed = mixed
 */
interface TypeParserInterface
{
    /**
     * Parses the provided source code and returns a complete AST.
     *
     * This method performs strict syntax validation. If the source contains
     * any syntax errors, parsing is aborted and an exception is thrown.
     *
     * Use this method when a fully valid syntax tree is required.
     *
     * ```
     * $parser->parse('array{ field: result }');
     * // => TypeNode
     *
     * $parser->parse('array{');
     * // => ParseException
     * ```
     *
     * @param TSource $source source code to parse
     * @throws ParserExceptionInterface in case of parsing exception occurs
     * @throws \Throwable in case of internal error occurs
     */
    public function parse(#[Language('PHP')] mixed $source): TypeNode;

    /**
     * Parses as much of the provided source code as possible and returns
     * a parsing result regardless of syntax errors.
     *
     * Unlike {@see self::parse()}, this method does not require the source
     * to be syntactically valid. It returns a partial AST together with
     * information about the parsing progress, including the offset of the last
     * successfully parsed token or construct.
     *
     * This mode is intended for IDE features, code completion, diagnostics,
     * phpdoc/docblock analysis and incremental analysis of incomplete source
     * code.
     *
     * ```
     * $result = $parser->tolerant('array{ field: result } This is an example');
     *
     * $result->type;
     * // => NamedTypeNode{ name: "array", ...
     *
     * $result->offset;
     * // => 23
     * ```
     *
     * To get information about the remaining part, just apply `substr` to source:
     * ```
     * $source = 'array{ field: result } This is an example';
     * $result = $parser->tolerant($source);
     *
     * echo substr($source, $result->offset);
     * // => "This is an example"
     * ```
     *
     * @param TSource $source source code to parse
     * @return ParsedResult parsing result containing a partial AST, parser
     *         state, and syntax diagnostics
     * @throws ParserExceptionInterface in case of parsing exception occurs
     * @throws \Throwable in case of internal error occurs
     */
    public function parseTolerant(#[Language('PHP')] mixed $source): ParsedResult;
}
