<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class BoolLiteralNode extends LiteralNode
{
    public function __construct(
        public readonly bool $value,
        string $raw = null,
    ) {
        parent::__construct($raw ?? ($value ? 'true' : 'false'));
    }

    public static function parse(string $value): self
    {
        $evaluated = \strtolower($value) === 'true';

        return new self($evaluated, $value);
    }
}
