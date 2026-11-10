<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\VariableNode;

/**
 * The variable `$name` is a node of its own: the name behind the dollar sign
 * and the offset of the dollar sign. It is read as the name of a parameter of
 * a callable.
 */
final class VariableNodeTest extends TestCase
{
    /**
     * The length of the "callable(" the variable is written behind.
     */
    private const PREFIX = 9;

    private function parseParameter(string $variable): CallableParameterNode
    {
        $node = $this->parse('callable(' . $variable . ')');

        self::assertInstanceOf(CallableTypeNode::class, $node);

        $parameter = $node->parameters[0];

        self::assertInstanceOf(CallableParameterNode::class, $parameter);

        return $parameter;
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function nameDataProvider(): iterable
    {
        yield 'letter' => ['$a', 'a'];
        yield 'word' => ['$value', 'value'];
        yield 'underscore in front' => ['$_x', '_x'];
        yield 'upper case' => ['$ABC', 'ABC'];
        yield 'camel case' => ['$someValue', 'someValue'];
        yield 'unicode' => ['$Ф', 'Ф'];
        yield 'this and a suffix' => ['$thisFoo', 'thisFoo'];
        yield 'this in upper case' => ['$THIS', 'THIS'];
        yield 'with trivia around' => ["/* c */ \$a // c\n", 'a'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $name
     */
    #[DataProvider('nameDataProvider')]
    public function testNameIsTheTextBehindTheDollarSign(string $source, string $name): void
    {
        $variable = $this->parseParameter($source)->name;

        self::assertInstanceOf(VariableNode::class, $variable);
        self::assertSame($name, $variable->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        // The offset of the dollar sign in the source, not counting the prefix
        yield 'no trivia' => ['$a', 0];
        yield 'spaces before' => ['  $a', 2];
        yield 'line breaks before' => ["\n\n\$a", 2];
        yield 'comment before' => ['/* c */$a', 7];
        yield 'multibyte comment before' => ['/* Фу */ $a', 11];
    }

    /**
     * The node begins at the dollar sign.
     *
     * @param non-empty-string $source
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetIsTheOffsetOfTheDollarSign(string $source, int $offset): void
    {
        $variable = $this->parseParameter($source)->name;

        self::assertInstanceOf(VariableNode::class, $variable);
        self::assertSame(self::PREFIX + $offset, $variable->offset);
    }

    public function testThisIsATypeNotAVariable(): void
    {
        $parameter = $this->parseParameter('$this');

        self::assertInstanceOf(ThisTypeNode::class, $parameter->type);
        self::assertNull($parameter->name);
    }

    public function testVariableIsNoType(): void
    {
        $this->expectException(\Throwable::class);

        $this->parse('$a');
    }
}
