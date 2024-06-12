<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Lexer\Token\Renderer;
use Phplrt\Lexer\Token\Token;
use Phplrt\Position\Position;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Exception
 */
final class Formatter
{
    /**
     * @return non-empty-string
     */
    public static function token(string $expr): string
    {
        return match ($expr) {
            "\0", '' => 'end of input',
            '"' => 'double quote (")',
            default => \sprintf('"%s"', \addcslashes($expr, '"')),
        };
    }

    public static function source(string $statement): string
    {
        $statement = \trim($statement);

        if ($statement === '') {
            return '<empty statement>';
        }

        $renderer = new Renderer();

        return $renderer->value(new Token('<statement>', $statement, 0));
    }

    /**
     * @param int<0, max> $offset
     *
     * @return non-empty-string
     */
    public static function suffix(string $statement, int $offset): string
    {
        if (\str_contains($statement, "\n")) {
            $pos = Position::fromOffset($statement, $offset);

            return \sprintf('on line %d at column %d', $pos->getLine(), $pos->getColumn());
        }

        return \sprintf('at column %d', $offset + 1);
    }
}
