<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class BoolLiteral extends Literal
{
    /**
     * @param int<0, max> $offset
     * @param bool $value
     */
    public function __construct(int $offset, public readonly bool $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return self
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset(), \strtolower($token->getValue()) === 'true');
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }
}
