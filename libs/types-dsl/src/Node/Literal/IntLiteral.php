<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class IntLiteral extends Literal
{
    /**
     * @param int<0, max> $offset
     * @param int $value
     */
    public function __construct(int $offset, public readonly int $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset(), (int)$token->getValue());
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
