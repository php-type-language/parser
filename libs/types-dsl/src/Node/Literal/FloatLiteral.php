<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class FloatLiteral extends Literal
{
    /**
     * @param int<0, max> $offset
     * @param float $value
     */
    public function __construct(int $offset, public readonly float $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset(), (float)$token->getValue());
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
