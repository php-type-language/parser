<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class BoolLiteralStmt extends Literal
{
    /**
     * @param bool $value
     */
    public function __construct(
        public readonly bool $value
    ) {
    }

    /**
     * @param TokenInterface $token
     * @return self
     */
    public static function parse(TokenInterface $token): self
    {
        return new self(\strtolower($token->getValue()) === 'true');
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }
}
