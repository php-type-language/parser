<?php

declare(strict_types=1);

namespace Hyper\Type\DSL\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Type\DSL
 */
class NullLiteral extends Literal
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(int $offset)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset());
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return null;
    }
}
