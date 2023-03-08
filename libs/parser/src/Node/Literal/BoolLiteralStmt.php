<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class BoolLiteralStmt extends LiteralStmt
{
    public function __construct(
        public readonly bool $value
    ) {
    }

    public static function parse(TokenInterface $token): self
    {
        return new self(\strtolower($token->getValue()) === 'true');
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
