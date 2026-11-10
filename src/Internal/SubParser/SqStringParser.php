<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 */
final class SqStringParser extends StringParser
{
    #[\Override]
    protected static function unpack(string $value): string
    {
        return \strtr(\substr($value, 1, -1), ["\'" => "'"]);
    }

    #[\Override]
    protected static function decode(string $value): string
    {
        return \strtr($value, ['\\\\' => '\\']);
    }

    public function parse(string $raw, int $offset = 0): StringLiteralNode
    {
        return new StringLiteralNode(
            value: self::decode(self::unpack($raw)),
            kind: StringLiteralKind::SingleQuoted,
            raw: $raw,
            offset: $offset,
        );
    }
}
