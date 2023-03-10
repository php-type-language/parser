<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class FloatLiteralStmt extends LiteralStmt
{
    public readonly string $raw;

    public function __construct(
        public readonly float $value,
        string $raw = null,
    ) {
        $this->raw = $raw ?? (string)$this->value;
    }

    /**
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self((float)$token->getValue(), $token->getValue());
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
