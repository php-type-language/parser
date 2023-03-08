<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class FloatLiteralStmt extends Literal
{
    /**
     * @param float $value
     */
    public function __construct(
        public readonly float $value,
    ) {
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self((float)$token->getValue());
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
