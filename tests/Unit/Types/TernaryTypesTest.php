<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Unit\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('unit'), Group('type-lang/parser')]
class TernaryTypesTest extends TypesTestCase
{
    private static function padding(string $message, int $size = 2): string
    {
        $prefix = \str_repeat('  ', $size);
        $lines = [];

        foreach (\explode("\n", $message) as $line) {
            $lines[] = $prefix . $line;
        }

        return \ltrim(\implode("\n", $lines));
    }

    public static function typesDataProvider(): iterable
    {
        yield 'T1' => ['T1', <<<'STMT'
            Stmt\NamedTypeNode
              Name(T1)
                Identifier(T1)
            STMT];

        yield 'foo()' => ['foo()', <<<'STMT'
            Stmt\CallableTypeNode
              Name(foo)
                Identifier(foo)
              Stmt\Callable\CallableParametersListNode
            STMT];

        yield '$this' => ['$this', <<<'STMT'
            Literal\VariableLiteralNode($this)
            STMT];

        yield '$var' => ['$var', <<<'STMT'
            Literal\VariableLiteralNode($var)
            STMT];
    }

    public static function twoTypesDataProvider(): iterable
    {
        foreach (self::typesDataProvider() as $t1Name => [$t1, $t1Ast]) {
            foreach (self::typesDataProvider() as $t2Name => [$t2, $t2Ast]) {
                yield $t1Name . ' + ' . $t2Name => [
                    $t1, $t1Ast,
                    $t2, $t2Ast,
                ];
            }
        }
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testEqual(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s is %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\EqualConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testNotEqual(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s is not %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\NotEqualConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testGreaterThan(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s > %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\GreaterThanConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testLessThan(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s < %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\LessThanConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testGreaterOrEqualThan(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s >= %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\GreaterOrEqualThanConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }

    #[DataProvider('twoTypesDataProvider')]
    public function testLessOrEqualThan(string $t1, string $t1Ast, string $t2, string $t2Ast): void
    {
        $this->assertTypeStatementSame(
            statement: \sprintf('%s <= %s ? U : V', $t1, $t2),
            expected: \vsprintf(<<<'OUTPUT'
                Stmt\TernaryConditionNode
                  Stmt\Condition\LessOrEqualThanConditionNode
                    %s
                    %s
                  Stmt\NamedTypeNode
                    Name(U)
                      Identifier(U)
                  Stmt\NamedTypeNode
                    Name(V)
                      Identifier(V)
                OUTPUT, [
                    self::padding($t1Ast, 2),
                    self::padding($t2Ast, 2),
                ]));
    }
}
