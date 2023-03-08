<?php

declare(strict_types=1);

namespace Hyper\Parser\Tests;

use Hyper\Parser\Node\Literal\BoolLiteralStmt;
use Hyper\Parser\Node\Literal\FloatLiteralStmt;
use Hyper\Parser\Node\Literal\IntLiteralStmt;
use Hyper\Parser\Node\Literal\NullLiteralStmt;
use Hyper\Parser\Node\Literal\StringLiteralStmt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('parser')]
class LiteralTestCase extends TestCase
{
    #[DataProvider('floatLiteralsDataProvider')]
    public function testFloatLiteral(string $expr, float $value): void
    {
        $ast = $this->parse($expr);

        $this->assertInstanceOf(FloatLiteralStmt::class, $ast);
        $this->assertSame($value, $ast->value);
    }

    #[DataProvider('intLiteralsDataProvider')]
    public function testIntLiteral(string $expr, int $value): void
    {
        $ast = $this->parse($expr);

        $this->assertInstanceOf(IntLiteralStmt::class, $ast);
        $this->assertSame($value, $ast->value);
    }

    #[DataProvider('boolLiteralsDataProvider')]
    public function testBoolLiteral(string $expr, bool $value): void
    {
        $ast = $this->parse($expr);

        $this->assertInstanceOf(BoolLiteralStmt::class, $ast);
        $this->assertSame($value, $ast->value);
    }

    #[DataProvider('nullLiteralsDataProvider')]
    public function testNullLiteral(string $expr): void
    {
        $ast = $this->parse($expr);

        $this->assertInstanceOf(NullLiteralStmt::class, $ast);
    }

    #[DataProvider('stringLiteralsDataProvider')]
    public function testStringLiteral(string $expr, string $value): void
    {
        $ast = $this->parse($expr);

        $this->assertInstanceOf(StringLiteralStmt::class, $ast);
        $this->assertSame($value, $ast->value);
    }

    protected function floatLiteralsDataProvider(): array
    {
        return [
            '1.234'  => ['1.234', 1.234],
            '1.2e3'  => ['1.2e3', 1.2e3],
            '7e3'    => ['7e3', 7e3],
            '7e-3'   => ['7e-3', 7e-3],
            '1.2E3'  => ['1.2E3', 1.2E3],
            '7E3'    => ['7E3', 7E3],
            '7E-3'   => ['7E-3', 7E-3],
            '.42'    => ['.42', .42],
            '23.'    => ['23.', 23.],
            '-1.234' => ['-1.234', -1.234],
            '-1.2e3' => ['-1.2e3', -1.2e3],
            '-7e3'   => ['-7e3', -7e3],
            '-7e-3'  => ['-7e-3', -7e-3],
            '-1.2E3' => ['-1.2E3', -1.2E3],
            '-7E3'   => ['-7E3', -7E3],
            '-7E-3'  => ['-7E-3', -7E-3],
            '-.42'   => ['-.42', -.42],
            '-23.'   => ['-23.', -23.],
        ];
    }

    protected function intLiteralsDataProvider(): array
    {
        return [
            '1234'        => ['1234', 1234],
            '-1234'       => ['-1234', -1234],
            '1_234_567'   => ['1_234_567', 1_234_567],
            '-1_234_567'  => ['-1_234_567', -1_234_567],
            '0123'        => ['0123', 0123],
            '-0123'       => ['-0123', -0123],
            '0o123'       => ['0o123', 0o123],
            '0O123'       => ['0O123', 0O123],
            '-0o123'      => ['-0o123', -0o123],
            '-0O123'      => ['-0O123', -0O123],
            '0x1a'        => ['0x1a', 0x1a],
            '0X1A'        => ['0X1A', 0X1A],
            '-0x1a'       => ['-0x1a', -0x1a],
            '-0X1A'       => ['-0X1A', -0X1A],
            '0b11111111'  => ['0b11111111', 0b11111111],
            '0B11111111'  => ['0B11111111', 0B11111111],
            '-0b11111111' => ['-0b11111111', -0b11111111],
            '-0B11111111' => ['-0B11111111', -0B11111111],
        ];
    }

    protected function boolLiteralsDataProvider(): array
    {
        return [
            'true'  => ['true', true],
            'True'  => ['True', True],
            'TRUE'  => ['TRUE', TRUE],
            'false' => ['false', false],
            'False' => ['False', False],
            'FALSE' => ['FALSE', FALSE],
        ];
    }

    protected function nullLiteralsDataProvider(): array
    {
        return [
            'null'  => ['null'],
            'Null'  => ['Null'],
            'NULL'  => ['NULL'],
        ];
    }

    protected function stringLiteralsDataProvider(): array
    {
        $single  = static fn(string $value): string => "'" . \addcslashes($value, "'") . "'";
        $_single = static fn(string $value): string => 'single quoted ' . $single($value);
        $double  = static fn(string $value): string => '"' . \addcslashes($value, '"') . '"';
        $_double = static fn(string $value): string => 'double quoted ' . $double($value);

        return [
            $_single('sample') => [$single('sample'), 'sample'],
            $_double('sample') => [$double('sample'), 'sample'],

            // Multiline strings
            $_single('sam
            ple') => [$single('sam
            ple'), 'sam
            ple'],
            $_double('sam
            ple') => [$double('sam
            ple'), 'sam
            ple'],

            // Escape sequences
            $_single('\n') => [$single('\n'), '\n'],
            $_double('\n') => [$double('\n'), "\n"],
            $_single('\r') => [$single('\r'), '\r'],
            $_double('\r') => [$double('\r'), "\r"],
            $_single('\t') => [$single('\t'), '\t'],
            $_double('\t') => [$double('\t'), "\t"],
            $_single('\v') => [$single('\v'), '\v'],
            $_double('\v') => [$double('\v'), "\v"],
            $_single('\e') => [$single('\e'), '\e'],
            $_double('\e') => [$double('\e'), "\e"],
            $_single('\f') => [$single('\f'), '\f'],
            $_double('\f') => [$double('\f'), "\f"],
            $_single('\$') => [$single('\$'), '\$'],
            $_double('\$') => [$double('\$'), "\$"],

            // Multiple backslashes
            $_single('\\\\') => [$single('\\\\'), '\\\\'],
            $_double('\\\\') => [$double('\\\\'), "\\\\"],

            $_single('\\\t') => [$single('\\\t'), '\\\t'],
            $_double('\\\t') => [$double('\\\t'), "\\\t"],
            $_single('\\\\t') => [$single('\\\\t'), '\\\\t'],
            $_double('\\\\t') => [$double('\\\\t'), "\\\\t"],

            // Simple UTF (4 bytes)
            $_single('\u{000c}') => [$single('\u{000c}'), '\u{000c}'],
            $_double('\u{000c}') => [$double('\u{000c}'), "\u{000c}"],
            $_single('\u{000C}') => [$single('\u{000C}'), '\u{000C}'],
            $_double('\u{000C}') => [$double('\u{000C}'), "\u{000C}"],
            $_single('\U{000c}') => [$single('\U{000c}'), '\U{000c}'],
            $_double('\U{000c}') => [$double('\U{000c}'), "\U{000c}"],
            $_single('\U{000C}') => [$single('\U{000C}'), '\U{000C}'],
            $_double('\U{000C}') => [$double('\U{000C}'), "\U{000C}"],

            // Extra UTF (8 bytes)
            $_single('\u{1f60a}') => [$single('\u{1f60a}'), '\u{1f60a}'],
            $_double('\u{1f60a}') => [$double('\u{1f60a}'), "\u{1f60a}"],
            $_single('\u{1F60A}') => [$single('\u{1F60A}'), '\u{1F60A}'],
            $_double('\u{1F60A}') => [$double('\u{1F60A}'), "\u{1F60A}"],
            $_single('\U{1f60a}') => [$single('\U{1f60a}'), '\U{1f60a}'],
            $_double('\U{1f60a}') => [$double('\U{1f60a}'), "\U{1f60a}"],
            $_single('\U{1F60A}') => [$single('\U{1F60A}'), '\U{1F60A}'],
            $_double('\U{1F60A}') => [$double('\U{1F60A}'), "\U{1F60A}"],

            // Hexdec byte notation
            $_single('\x9c') => [$single('\x9c'), '\x9c'],
            $_double('\x9c') => [$double('\x9c'), "\x9c"],
            $_single('\x9C') => [$single('\x9C'), '\x9C'],
            $_double('\x9C') => [$double('\x9C'), "\x9C"],
            $_single('\X9c') => [$single('\X9c'), '\X9c'],
            $_double('\X9c') => [$double('\X9c'), "\X9c"],
            $_single('\X9C') => [$single('\X9C'), '\X9C'],
            $_double('\X9C') => [$double('\X9C'), "\X9C"],
        ];
    }
}
