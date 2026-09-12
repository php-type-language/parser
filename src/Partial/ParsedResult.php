<?php

declare(strict_types=1);

namespace TypeLang\Parser\Partial;

/**
 * The outcome of reading as much of a source as the grammar describes.
 *
 * ```
 *  array{a: int} and more
 *  ^^^^^^^^^^^^^          the part a type is read out of
 *                ^^^^^^^^ the part left unread
 * ```
 *
 * @phpstan-sealed FailureParsedResult|PartialParsedResult|SuccessfulParsedResult
 */
abstract class ParsedResult {}
