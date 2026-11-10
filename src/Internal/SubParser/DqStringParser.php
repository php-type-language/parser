<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Parser\Internal\SubParser\StringParser\StringSequencesFetcher;
use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class DqStringParser extends StringParser
{
    #[\Override]
    protected static function unpack(string $value): string
    {
        return \strtr(\substr($value, 1, -1), ['\\"' => '"']);
    }

    #[\Override]
    protected static function decode(string $value): string
    {
        if (!\str_contains($value, '\\')) {
            return $value;
        }

        return \strtr($value, StringSequencesFetcher::get($value));
    }

    public function parse(string $raw, int $offset = 0): StringLiteralNode
    {
        return new StringLiteralNode(
            value: self::decode(self::unpack($raw)),
            kind: StringLiteralKind::DoubleQuoted,
            raw: $raw,
            offset: $offset,
        );
    }
}
