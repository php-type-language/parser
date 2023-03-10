<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal TypeLang\Parser
 */
class BoolLiteralStmt extends LiteralStmt
{
    public readonly string $raw;

    public function __construct(
        public readonly bool $value,
        string $raw = null,
    ) {
        $this->raw = $raw ?? ($this->value ? 'true' : 'false');
    }

    public static function parse(TokenInterface $token): self
    {
        return new self(
            \strtolower($token->getValue()) === 'true',
            $token->getValue(),
        );
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
