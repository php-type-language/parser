<?php

declare(strict_types=1);

namespace Hyper\Parser\Node\Literal;

use Phplrt\Lexer\Token\CompositeTokenInterface;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal Hyper\Parser
 */
class StringLiteralStmt extends Literal
{
    /**
     * @var non-empty-string
     */
    private const UTF_SEQUENCE_PATTERN = '/(?<!\\\\)\\\\u\{([0-9a-fA-F]+)}/u';

    /**
     * @var non-empty-string
     */
    private const HEX_SEQUENCE_PATTERN = '/(?<!\\\\)\\\\x([0-9a-fA-F]{1,2})/iu';

    /**
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    private const ESCAPED_CHARS = [
        '\n' => "\n",
        '\r' => "\r",
        '\t' => "\t",
        '\v' => "\v",
        '\e' => "\e",
        '\f' => "\f",
        '\$' => "\$",
    ];

    /**
     * @param string $value
     */
    final public function __construct(
        public readonly string $value,
    ) {
    }

    /**
     * @return self
     */
    public static function parse(CompositeTokenInterface $token, bool $encoded = true): self
    {
        if ($encoded) {
            $string = \str_replace('\"', '"', $token[0]->getValue());
            return self::fromEncodedString($string);
        }

        $string = \str_replace("\'", "'", $token[0]->getValue());
        return self::fromRawString($string);
    }

    public static function fromRawString(string $string): self
    {
        return new self($string);
    }

    /**
     * @return self
     */
    public static function fromEncodedString(string $string): self
    {
        if (\str_contains($string, '\\')) {
            // Replace double backslash to "\0"
            $string = \str_replace('\\\\', "\0", $string);

            // Replace escaped chars (like a "\n") to real bytes
            $string = self::renderEscapeSequences($string);

            // Replace hex sequences (like a "\xFF") to real bytes
            $string = self::renderHexadecimalSequences($string);

            // Replace unicode sequences (like a "\u{FFFF}") to real bytes
            $string = self::renderUtfSequences($string);

            // Rollback double backslash escaping
            $string = \str_replace("\0", '\\\\', $string);
        }

        return self::fromRawString($string);
    }

    /**
     * Method for parsing and decode special escaped character sequences
     * like a "\n", "\r" etc...
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     */
    private static function renderEscapeSequences(string $body): string
    {
        return \str_replace(
            \array_keys(self::ESCAPED_CHARS),
            \array_values(self::ESCAPED_CHARS),
            $body,
        );
    }

    /**
     * Method for parsing and decode hexadecimal character sequences
     * like a "\xFF" type.
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     */
    private static function renderHexadecimalSequences(string $body): string
    {
        $callee = static function (array $matches): string {
            return \chr(\hexdec($matches[1]));
        };

        return @\preg_replace_callback(self::HEX_SEQUENCE_PATTERN, $callee, $body) ?? $body;
    }

    /**
     * Method for parsing and decode utf-8 character sequences
     * like a "\u{FFFF}" type.
     *
     * @link https://www.php.net/manual/en/language.types.string.php
     */
    private static function renderUtfSequences(string $body): string
    {
        $callee = static function (array $matches): string {
            $code = \hexdec($matches[1]);

            if (\function_exists('\\mb_chr')) {
                return \mb_chr($code);
            }

            if (0x80 > $code %= 0x200000) {
                return \chr($code);
            } elseif (0x800 > $code) {
                return \chr(0xC0 | $code >> 6)
                     . \chr(0x80 | $code & 0x3F);
            } elseif (0x10000 > $code) {
                return \chr(0xE0 | $code >> 12)
                     . \chr(0x80 | $code >> 6 & 0x3F)
                     . \chr(0x80 | $code & 0x3F);
            }

            return \chr(0xF0 | $code >> 18)
                 . \chr(0x80 | $code >> 12 & 0x3F)
                 . \chr(0x80 | $code >> 6 & 0x3F)
                 . \chr(0x80 | $code & 0x3F);
        };

        return @\preg_replace_callback(self::UTF_SEQUENCE_PATTERN, $callee, $body) ?? $body;
    }
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
