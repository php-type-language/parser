<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class IntLiteralStmt extends Literal
{
    /**
     * @param int $value
     */
    final public function __construct(
        public readonly int $value,
    ) {
    }

    /**
     * @param TokenInterface $token
     * @return self
     */
    public static function parse(TokenInterface $token): self
    {
        return self::fromString($token->getValue());
    }

    /**
     * @param numeric-string $expr
     *
     * @return self
     */
    public static function fromString(string $expr): self
    {
        [$isNegative, $decimal] = self::split($expr);

        return new self($isNegative ? -$decimal : $decimal);
    }

    /**
     * @param numeric-string $literal
     *
     * @return array{bool, int>
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($isNegative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            return [$isNegative, match ($literal[1]) {
                // hexadecimal
                'x', 'X' => \hexdec(\substr($literal, 2)),
                // binary
                'b', 'B' => \bindec(\substr($literal, 2)),
                // octal
                'o', 'O' => \octdec(\substr($literal, 2)),
                // octal (legacy)
                default => \octdec($literal),
            }];
        }

        return [$isNegative, (int)$literal];
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}